<?php

namespace Database\Seeders;

use App\Models\HotelService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HotelServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->services() as $index => $data) {
            $imagePath = 'seed/services/service-'.($index + 1).'.jpg';
            $sourceIndex = ($index % 4) + 1;
            $this->copyPublicAsset("landing-lage/img/gallery/gallery-{$sourceIndex}.jpg", $imagePath);
            $service = HotelService::query()->withTrashed()->updateOrCreate(
                ['code' => $data['code']],
                [...$data, 'image_path' => $imagePath]
            );
            $service->restore();
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function services(): array
    {
        return [
            ['code' => 'MASSAGE-60', 'name' => 'Traditional Massage 60 Menit', 'category' => 'massage', 'description' => 'Pijat tradisional selama 60 menit di area spa.', 'price' => 275000, 'price_unit' => 'per_order', 'duration_minutes' => 60, 'requires_schedule' => true, 'is_available' => true, 'is_active' => true, 'sort_order' => 1],
            ['code' => 'SPA-PACKAGE', 'name' => 'Candra Spa Package', 'category' => 'spa', 'description' => 'Paket relaksasi lengkap dengan massage dan body treatment.', 'price' => 450000, 'price_unit' => 'per_order', 'duration_minutes' => 120, 'requires_schedule' => true, 'is_available' => true, 'is_active' => true, 'sort_order' => 2],
            ['code' => 'LAUNDRY-KG', 'name' => 'Laundry', 'category' => 'laundry', 'description' => 'Layanan cuci dan setrika pakaian tamu.', 'price' => 35000, 'price_unit' => 'per_kg', 'duration_minutes' => null, 'requires_schedule' => false, 'is_available' => true, 'is_active' => true, 'sort_order' => 3],
            ['code' => 'EXTRA-BED', 'name' => 'Extra Bed', 'category' => 'extra_bed', 'description' => 'Tambahan tempat tidur untuk satu malam.', 'price' => 200000, 'price_unit' => 'per_order', 'duration_minutes' => null, 'requires_schedule' => false, 'is_available' => true, 'is_active' => true, 'sort_order' => 4],
            ['code' => 'AIRPORT-PICKUP', 'name' => 'Airport Pickup', 'category' => 'transport', 'description' => 'Penjemputan dari bandara menuju Candra Resort.', 'price' => 350000, 'price_unit' => 'per_order', 'duration_minutes' => null, 'requires_schedule' => true, 'is_available' => true, 'is_active' => true, 'sort_order' => 5],
            ['code' => 'AIRPORT-DROPOFF', 'name' => 'Airport Drop-off', 'category' => 'transport', 'description' => 'Pengantaran dari Candra Resort menuju bandara.', 'price' => 350000, 'price_unit' => 'per_order', 'duration_minutes' => null, 'requires_schedule' => true, 'is_available' => true, 'is_active' => true, 'sort_order' => 6],
        ];
    }

    private function copyPublicAsset(string $source, string $destination): void
    {
        $sourcePath = public_path($source);
        if (is_file($sourcePath) && ! Storage::disk('public')->exists($destination)) {
            Storage::disk('public')->put($destination, file_get_contents($sourcePath));
        }
    }
}
