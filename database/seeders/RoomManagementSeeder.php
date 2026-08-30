<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RoomManagementSeeder extends Seeder
{
    public function run(): void
    {
        $facilities = collect($this->facilities())->mapWithKeys(function (array $data): array {
            $facility = Facility::query()->withTrashed()->updateOrCreate(['slug' => $data['slug']], $data);
            $facility->restore();

            return [$facility->slug => $facility];
        });

        foreach ($this->roomTypes() as $data) {
            $facilitySlugs = $data['facilities'];
            $roomNumbers = $data['rooms'];
            $images = $data['images'];
            unset($data['facilities'], $data['rooms'], $data['images']);

            $roomType = RoomType::query()->withTrashed()->updateOrCreate(['code' => $data['code']], $data);
            $roomType->restore();
            $roomType->facilities()->sync($facilities->only($facilitySlugs)->pluck('id')->all());

            foreach ($roomNumbers as $roomNumber) {
                $room = Room::query()->withTrashed()->firstOrCreate(
                    ['room_number' => $roomNumber],
                    [
                        'room_type_id' => $roomType->id,
                        'floor' => substr($roomNumber, 0, 1),
                        'status' => RoomStatus::Available,
                        'is_active' => true,
                    ]
                );
                $room->restore();
                $room->update(['room_type_id' => $roomType->id, 'floor' => substr($roomNumber, 0, 1), 'is_active' => true]);
            }

            foreach ($images as $sortOrder => $sourceName) {
                $path = "seed/rooms/{$roomType->slug}-".($sortOrder + 1).'.jpg';
                $this->copyPublicAsset("landing-lage/img/room/{$sourceName}", $path);
                RoomTypeImage::query()->updateOrCreate(
                    ['room_type_id' => $roomType->id, 'sort_order' => $sortOrder + 1],
                    [
                        'image_path' => $path,
                        'alt_text' => $roomType->name.' '.($sortOrder + 1),
                        'caption' => 'Suasana '.$roomType->name,
                        'is_primary' => $sortOrder === 0,
                    ]
                );
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function facilities(): array
    {
        return [
            ['name' => 'Wi-Fi', 'slug' => 'wi-fi', 'scope' => 'both', 'icon' => 'flaticon-036-parking', 'description' => 'Internet tersedia di area hotel dan kamar.', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Restaurant', 'slug' => 'restaurant', 'scope' => 'hotel', 'icon' => 'flaticon-033-dinner', 'description' => 'Pilihan makanan dan minuman sepanjang hari.', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Housekeeping', 'slug' => 'housekeeping', 'scope' => 'both', 'icon' => 'flaticon-024-towel', 'description' => 'Layanan kebersihan untuk kenyamanan tamu.', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Air Conditioner', 'slug' => 'air-conditioner', 'scope' => 'room', 'icon' => 'flaticon-026-bed', 'description' => 'Pendingin ruangan di setiap kamar.', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Smart TV', 'slug' => 'smart-tv', 'scope' => 'room', 'icon' => 'flaticon-026-bed', 'description' => 'Hiburan di dalam kamar.', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Parking Area', 'slug' => 'parking-area', 'scope' => 'hotel', 'icon' => 'flaticon-036-parking', 'description' => 'Area parkir untuk tamu hotel.', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Kolam Renang', 'slug' => 'kolam-renang', 'scope' => 'hotel', 'icon' => 'flaticon-019-television', 'description' => 'Kolam renang untuk dewasa dan anak.', 'is_active' => true, 'sort_order' => 7],
            ['name' => 'Air Panas', 'slug' => 'air-panas', 'scope' => 'room', 'icon' => 'flaticon-024-towel', 'description' => 'Air panas tersedia di kamar mandi.', 'is_active' => true, 'sort_order' => 8],
            ['name' => 'Coffee Maker', 'slug' => 'coffee-maker', 'scope' => 'room', 'icon' => 'flaticon-033-dinner', 'description' => 'Perlengkapan kopi dan teh di dalam kamar.', 'is_active' => true, 'sort_order' => 9],
            ['name' => 'Resepsionis 24 Jam', 'slug' => 'resepsionis-24-jam', 'scope' => 'hotel', 'icon' => 'flaticon-044-clock-1', 'description' => 'Bantuan Receptionist tersedia 24 jam.', 'is_active' => true, 'sort_order' => 10],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function roomTypes(): array
    {
        return [
            ['code' => 'DLX', 'name' => 'Deluxe Room', 'slug' => 'deluxe-room', 'description' => 'Kamar modern dengan kenyamanan lengkap untuk dua tamu.', 'capacity' => 2, 'max_adults' => 2, 'max_children' => 1, 'bed_type' => 'King Bed', 'bed_count' => 1, 'room_size_sqm' => 30, 'base_price' => 750000, 'extra_bed_price' => 200000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 1, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv', 'air-panas'], 'rooms' => ['101', '102', '103'], 'images' => ['room-1.jpg', 'room-b1.jpg']],
            ['code' => 'PRM', 'name' => 'Premium King', 'slug' => 'premium-king', 'description' => 'Kamar premium dengan king bed, ruang lebih lega, dan fasilitas pilihan.', 'capacity' => 2, 'max_adults' => 2, 'max_children' => 1, 'bed_type' => 'King Bed', 'bed_count' => 1, 'room_size_sqm' => 36, 'base_price' => 925000, 'extra_bed_price' => 200000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 2, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv', 'air-panas', 'coffee-maker'], 'rooms' => ['201', '202', '203'], 'images' => ['room-2.jpg', 'room-b2.jpg']],
            ['code' => 'FAM', 'name' => 'Family Room', 'slug' => 'family-room', 'description' => 'Ruang luas untuk menikmati perjalanan bersama keluarga.', 'capacity' => 4, 'max_adults' => 3, 'max_children' => 2, 'bed_type' => 'King & Single Bed', 'bed_count' => 2, 'room_size_sqm' => 42, 'base_price' => 1100000, 'extra_bed_price' => 200000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 3, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv', 'air-panas'], 'rooms' => ['301', '302', '303'], 'images' => ['room-3.jpg', 'room-b3.jpg']],
            ['code' => 'STE', 'name' => 'Candra Suite', 'slug' => 'candra-suite', 'description' => 'Suite premium dengan area duduk dan layanan terbaik Candra Resort.', 'capacity' => 3, 'max_adults' => 2, 'max_children' => 1, 'bed_type' => 'Super King Bed', 'bed_count' => 1, 'room_size_sqm' => 55, 'base_price' => 1650000, 'extra_bed_price' => 250000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 4, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv', 'air-panas', 'coffee-maker'], 'rooms' => ['401', '402', '403'], 'images' => ['room-4.jpg', 'room-b4.jpg']],
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
