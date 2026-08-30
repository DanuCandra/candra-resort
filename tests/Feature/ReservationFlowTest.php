<?php

namespace Tests\Feature;

use App\Contracts\MidtransGateway;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomRate;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_reservation_uses_nightly_rate_and_promotion_snapshots(): void
    {
        $guest = User::factory()->create();
        $roomType = $this->roomType(2);
        $checkIn = today()->next(Carbon::SATURDAY);
        $checkOut = $checkIn->copy()->addDays(2);
        RoomRate::create([
            'room_type_id' => $roomType->id,
            'name' => 'Weekend Test Rate',
            'days_of_week' => [6, 7],
            'price_per_night' => 900000,
            'priority' => 20,
            'is_active' => true,
        ]);
        $promotion = Promotion::create([
            'code' => 'SAVE10TEST',
            'name' => 'Save Ten Test',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'minimum_transaction' => 0,
            'used_count' => 0,
            'is_active' => true,
        ]);
        $promotion->roomTypes()->attach($roomType);

        $response = $this->actingAs($guest)->post(route('guest.reservations.store'), [
            'room_type_id' => $roomType->id,
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'adults' => 2,
            'children' => 0,
            'promo_code' => 'save10test',
            'terms' => 1,
        ]);

        $reservation = Reservation::where('guest_id', $guest->id)->latest()->firstOrFail();
        $response->assertRedirect(route('guest.reservations.payment', $reservation));
        $this->assertSame('pending_payment', $reservation->status->value);
        $this->assertSame('1800000.00', $reservation->subtotal);
        $this->assertSame('180000.00', $reservation->discount_amount);
        $this->assertSame('1620000.00', $reservation->grand_total);
        $this->assertSame(2, $reservation->nights()->count());
        $this->assertDatabaseHas('reservation_nights', [
            'reservation_id' => $reservation->id,
            'rate_name' => 'Weekend Test Rate',
            'price_before_discount' => 900000,
            'discount_amount' => 90000,
            'net_price' => 810000,
        ]);
        $this->assertSame(1, $promotion->fresh()->used_count);
    }

    public function test_overlapping_reservation_cannot_exceed_physical_inventory(): void
    {
        $roomType = $this->roomType(1);
        $firstGuest = User::factory()->create();
        $secondGuest = User::factory()->create();
        $payload = [
            'room_type_id' => $roomType->id,
            'check_in' => today()->addDays(5)->toDateString(),
            'check_out' => today()->addDays(7)->toDateString(),
            'adults' => 1,
            'children' => 0,
            'terms' => 1,
        ];

        $this->actingAs($firstGuest)->post(route('guest.reservations.store'), $payload)->assertRedirect();
        $this->actingAs($secondGuest)->from(route('public.rooms.index'))->post(route('guest.reservations.store'), $payload)
            ->assertRedirect(route('public.rooms.index'))->assertSessionHasErrors('room_type_id');

        $this->assertSame(1, Reservation::where('room_type_id', $roomType->id)->count());
    }

    public function test_expired_hold_is_released_for_a_new_reservation(): void
    {
        $roomType = $this->roomType(1);
        $firstGuest = User::factory()->create();
        $secondGuest = User::factory()->create();
        $payload = [
            'room_type_id' => $roomType->id,
            'check_in' => today()->addDays(8)->toDateString(),
            'check_out' => today()->addDays(9)->toDateString(),
            'adults' => 1,
            'children' => 0,
            'terms' => 1,
        ];

        $this->actingAs($firstGuest)->post(route('guest.reservations.store'), $payload);
        $firstReservation = Reservation::where('guest_id', $firstGuest->id)->firstOrFail();
        $firstReservation->update(['payment_due_at' => now()->subMinute()]);

        $this->actingAs($secondGuest)->post(route('guest.reservations.store'), $payload)->assertRedirect();

        $this->assertSame('cancelled', $firstReservation->fresh()->status->value);
        $this->assertDatabaseHas('reservations', ['guest_id' => $secondGuest->id, 'status' => 'pending_payment']);
    }

    public function test_midtrans_checkout_and_signed_notification_confirm_reservation(): void
    {
        config()->set('services.midtrans.client_key', 'test-client-key');
        config()->set('services.midtrans.server_key', 'test-server-key');
        $this->app->instance(MidtransGateway::class, new class implements MidtransGateway
        {
            public function createSnapToken(array $parameters): string
            {
                return 'snap-token-test';
            }
        });

        PaymentMethod::query()->updateOrCreate(['code' => 'midtrans'], [
            'name' => 'Midtrans Test',
            'type' => 'gateway',
            'channel' => 'midtrans',
            'is_online' => true,
            'is_active' => true,
        ]);
        $guest = User::factory()->create();
        $reservation = $this->pendingReservation($guest, $this->roomType(1));

        $this->actingAs($guest)->get(route('guest.reservations.payment', $reservation))
            ->assertOk()->assertSee('snap-token-test');

        $payment = Payment::where('reservation_id', $reservation->id)->firstOrFail();
        $payload = [
            'order_id' => $payment->midtrans_order_id,
            'status_code' => '200',
            'gross_amount' => number_format((float) $payment->amount, 2, '.', ''),
            'transaction_status' => 'settlement',
            'transaction_id' => 'TRX-'.Str::upper(Str::random(12)),
            'payment_type' => 'bank_transfer',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].'test-server-key');

        $this->postJson(route('payments.midtrans.notification'), $payload)->assertOk();

        $this->assertSame('paid', $payment->fresh()->status->value);
        $this->assertSame('paid', $reservation->fresh()->status->value);
        $this->assertSame('paid', $reservation->fresh()->payment_status->value);
    }

    public function test_midtrans_notification_rejects_invalid_signature_and_amount(): void
    {
        config()->set('services.midtrans.server_key', 'test-server-key');
        $guest = User::factory()->create();
        $reservation = $this->pendingReservation($guest, $this->roomType(1));
        $method = PaymentMethod::query()->updateOrCreate(['code' => 'midtrans-callback-test'], [
            'name' => 'Midtrans Callback Test', 'type' => 'gateway', 'channel' => 'midtrans', 'is_online' => true, 'is_active' => true,
        ]);
        $payment = Payment::create([
            'payment_code' => 'PAY-'.Str::upper(Str::random(15)),
            'reservation_id' => $reservation->id,
            'payment_method_id' => $method->id,
            'purpose' => 'reservation',
            'status' => 'pending',
            'source' => 'guest',
            'currency' => 'IDR',
            'amount' => 700000,
            'midtrans_order_id' => 'MID-'.Str::upper(Str::random(12)),
        ]);

        $this->postJson(route('payments.midtrans.notification'), [
            'order_id' => $payment->midtrans_order_id,
            'status_code' => '200',
            'gross_amount' => '700000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'invalid',
        ])->assertUnprocessable();

        $this->assertSame('pending', $payment->fresh()->status->value);
        $this->assertSame('pending_payment', $reservation->fresh()->status->value);
    }

    public function test_guest_cannot_open_another_guests_reservation(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $reservation = $this->pendingReservation($owner, $this->roomType(1));

        $this->actingAs($intruder)->get(route('guest.reservations.show', $reservation))->assertForbidden();
        $this->actingAs($intruder)->get(route('guest.reservations.payment', $reservation))->assertForbidden();
    }

    private function roomType(int $rooms): RoomType
    {
        $roomType = RoomType::create([
            'code' => 'BOOK-'.Str::upper(Str::random(7)),
            'name' => 'Booking Room '.Str::random(7),
            'slug' => 'booking-room-'.Str::lower(Str::random(10)),
            'capacity' => 3,
            'max_adults' => 2,
            'max_children' => 1,
            'bed_count' => 1,
            'base_price' => 700000,
            'extra_bed_price' => 0,
            'is_active' => true,
        ]);

        foreach (range(1, $rooms) as $index) {
            Room::create([
                'room_type_id' => $roomType->id,
                'room_number' => 'B-'.Str::upper(Str::random(5)).$index,
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        return $roomType;
    }

    private function pendingReservation(User $guest, RoomType $roomType): Reservation
    {
        return Reservation::create([
            'booking_code' => 'CR-TEST-'.Str::upper(Str::random(8)),
            'guest_id' => $guest->id,
            'room_type_id' => $roomType->id,
            'source' => 'online',
            'guest_name' => $guest->name,
            'guest_email' => $guest->email,
            'guest_phone' => $guest->phone,
            'check_in_date' => today()->addDays(3),
            'check_out_date' => today()->addDays(4),
            'total_nights' => 1,
            'adults' => 1,
            'children' => 0,
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'currency' => 'IDR',
            'subtotal' => 700000,
            'grand_total' => 700000,
            'payment_due_at' => now()->addMinutes(30),
        ]);
    }
}
