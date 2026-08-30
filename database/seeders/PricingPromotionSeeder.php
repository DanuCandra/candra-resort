<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\RoomRate;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;

class PricingPromotionSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = User::query()->where('employee_code', 'OWN-001')->value('id');
        $roomTypes = RoomType::query()->whereIn('code', ['DLX', 'PRM', 'FAM', 'STE'])->get()->keyBy('code');

        foreach ($roomTypes as $roomType) {
            $weekendRate = RoomRate::query()->withTrashed()->updateOrCreate(
                ['room_type_id' => $roomType->id, 'name' => 'Weekend Rate'],
                [
                    'start_date' => null,
                    'end_date' => null,
                    'days_of_week' => [6, 7],
                    'price_per_night' => round((float) $roomType->base_price * 1.15),
                    'priority' => 10,
                    'is_active' => true,
                    'created_by' => $ownerId,
                ]
            );
            $weekendRate->restore();

            $highSeasonRate = RoomRate::query()->withTrashed()->updateOrCreate(
                ['room_type_id' => $roomType->id, 'name' => 'High Season'],
                [
                    'start_date' => now()->addMonths(5)->startOfMonth()->toDateString(),
                    'end_date' => now()->addMonths(5)->endOfMonth()->toDateString(),
                    'days_of_week' => null,
                    'price_per_night' => round((float) $roomType->base_price * 1.25),
                    'priority' => 20,
                    'is_active' => true,
                    'created_by' => $ownerId,
                ]
            );
            $highSeasonRate->restore();
        }

        $promotions = [
            ['code' => 'WELCOME10', 'name' => 'Welcome to Candra', 'description' => 'Diskon perkenalan untuk pengalaman menginap pertama di Candra Resort.', 'discount_type' => 'percent', 'discount_value' => 10, 'max_discount_amount' => 250000, 'minimum_transaction' => 750000, 'usage_quota' => 100, 'max_usage_per_guest' => 1, 'room_codes' => ['DLX', 'PRM', 'FAM', 'STE']],
            ['code' => 'STAY3', 'name' => 'Stay Longer, Save More', 'description' => 'Nikmati potongan khusus untuk reservasi dengan nilai minimum Rp2.000.000.', 'discount_type' => 'fixed', 'discount_value' => 300000, 'max_discount_amount' => null, 'minimum_transaction' => 2000000, 'usage_quota' => 75, 'max_usage_per_guest' => 2, 'room_codes' => ['PRM', 'FAM', 'STE']],
            ['code' => 'FAMILY15', 'name' => 'Family Holiday', 'description' => 'Diskon 15% untuk pengalaman liburan keluarga di Family Room.', 'discount_type' => 'percent', 'discount_value' => 15, 'max_discount_amount' => 500000, 'minimum_transaction' => 1500000, 'usage_quota' => 50, 'max_usage_per_guest' => 1, 'room_codes' => ['FAM']],
        ];

        foreach ($promotions as $data) {
            $roomCodes = $data['room_codes'];
            unset($data['room_codes']);
            $promotion = Promotion::query()->withTrashed()->updateOrCreate(
                ['code' => $data['code']],
                [
                    ...$data,
                    'starts_at' => now()->startOfDay(),
                    'ends_at' => now()->addMonths(6)->endOfDay(),
                    'is_active' => true,
                    'created_by' => $ownerId,
                ]
            );
            $promotion->restore();
            $promotion->roomTypes()->sync($roomTypes->only($roomCodes)->pluck('id')->all());
        }
    }
}
