<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FoundationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            HotelContentSeeder::class,
            RoomManagementSeeder::class,
            PricingPromotionSeeder::class,
            PaymentMethodSeeder::class,
            FoodBeverageSeeder::class,
            HotelServiceSeeder::class,
        ]);
    }
}
