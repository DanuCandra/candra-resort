<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Promotion;
use App\Models\RoomRate;
use App\Models\RoomType;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

// Menghitung tarif per malam dan diskon promosi.
class PricingService
{
    public function quote(
        RoomType $roomType,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        ?string $promoCode = null,
        ?User $guest = null,
        bool $lockPromotion = false,
    ): array {
        $rates = RoomRate::query()
            ->where('room_type_id', $roomType->id)
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('start_date')->orWhereDate('start_date', '<=', $checkOut->toDateString()))
            ->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', $checkIn->toDateString()))
            ->orderByDesc('priority')->orderByDesc('id')->get();

        $nights = [];
        $date = CarbonImmutable::parse($checkIn->toDateString());
        $lastDate = CarbonImmutable::parse($checkOut->toDateString());

        while ($date->lt($lastDate)) {
            $rate = $rates->first(fn (RoomRate $candidate): bool => $this->applies($candidate, $date));
            $price = (int) round((float) ($rate?->price_per_night ?? $roomType->base_price));
            $nights[] = [
                'stay_date' => $date->toDateString(),
                'room_rate_id' => $rate?->id,
                'rate_name' => $rate?->name ?? 'Harga Dasar',
                'price_before_discount' => $price,
            ];
            $date = $date->addDay();
        }

        $subtotal = collect($nights)->sum('price_before_discount');
        $promotion = $this->promotion($roomType, $promoCode, $subtotal, $guest, $lockPromotion);
        $discount = $this->discount($promotion, $subtotal);
        $remainingDiscount = $discount;

        foreach ($nights as $index => &$night) {
            $nightDiscount = $index === array_key_last($nights)
                ? $remainingDiscount
                : (int) round($discount * ($night['price_before_discount'] / max(1, $subtotal)));
            $nightDiscount = min($nightDiscount, $remainingDiscount, $night['price_before_discount']);
            $night['discount_amount'] = $nightDiscount;
            $night['net_price'] = $night['price_before_discount'] - $nightDiscount;
            $remainingDiscount -= $nightDiscount;
        }
        unset($night);

        return [
            'nights' => $nights,
            'total_nights' => count($nights),
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'grand_total' => $subtotal - $discount,
            'promotion' => $promotion,
        ];
    }

    private function applies(RoomRate $rate, CarbonInterface $date): bool
    {
        return ($rate->start_date === null || $rate->start_date->startOfDay()->lte($date))
            && ($rate->end_date === null || $rate->end_date->endOfDay()->gte($date))
            && (empty($rate->days_of_week) || in_array($date->isoWeekday(), $rate->days_of_week));
    }

    private function promotion(RoomType $roomType, ?string $promoCode, int $subtotal, ?User $guest, bool $lock): ?Promotion
    {
        if (! $promoCode) {
            return null;
        }

        $query = Promotion::query()->where('code', strtoupper(trim($promoCode)))->where('is_active', true);
        if ($lock) {
            $query->lockForUpdate();
        }

        $promotion = $query->first();
        $valid = $promotion
            && ($promotion->starts_at === null || $promotion->starts_at->lte(now()))
            && ($promotion->ends_at === null || $promotion->ends_at->gte(now()))
            && ($promotion->usage_quota === null || $promotion->used_count < $promotion->usage_quota)
            && (float) $promotion->minimum_transaction <= $subtotal
            && (! $promotion->roomTypes()->exists() || $promotion->roomTypes()->whereKey($roomType->id)->exists());

        if ($valid && $guest && $promotion->max_usage_per_guest !== null) {
            $usage = $promotion->reservations()->where('guest_id', $guest->id)
                ->whereNotIn('status', [ReservationStatus::Cancelled->value, ReservationStatus::NoShow->value])->count();
            $valid = $usage < $promotion->max_usage_per_guest;
        }

        if (! $valid) {
            throw ValidationException::withMessages(['promo_code' => 'Kode promosi tidak valid atau tidak memenuhi ketentuan.']);
        }

        return $promotion;
    }

    private function discount(?Promotion $promotion, int $subtotal): int
    {
        if (! $promotion) {
            return 0;
        }

        $discount = $promotion->discount_type === 'percent'
            ? (int) round($subtotal * ((float) $promotion->discount_value / 100))
            : (int) round((float) $promotion->discount_value);

        if ($promotion->max_discount_amount !== null) {
            $discount = min($discount, (int) round((float) $promotion->max_discount_amount));
        }

        return min($discount, $subtotal);
    }
}
