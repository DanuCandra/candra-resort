<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\HotelSetting;
use App\Models\PaymentMethod;
use App\Models\Promotion;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\WebsiteContent;
use Illuminate\Database\Seeder;

class FoundationSeeder extends Seeder
{
    public function run(): void
    {
        $ownerPassword = env('OWNER_PASSWORD');
        $receptionistPassword = env('RECEPTIONIST_PASSWORD');

        if (app()->isProduction() && (blank($ownerPassword) || blank($receptionistPassword))) {
            throw new \RuntimeException('OWNER_PASSWORD dan RECEPTIONIST_PASSWORD wajib diatur sebelum menjalankan seeder di production.');
        }

        $owner = User::query()->firstOrCreate(
            ['email' => env('OWNER_EMAIL', 'owner@candraresort.test')],
            [
                'name' => env('OWNER_NAME', 'Owner Candra Resort'),
                'username' => 'owner',
                'employee_code' => 'OWN-001',
                'phone' => '6281234567800',
                'role' => UserRole::Owner,
                'password' => $ownerPassword ?: 'CandraOwner123',
                'is_active' => true,
            ]
        );

        User::query()->firstOrCreate(
            ['email' => env('RECEPTIONIST_EMAIL', 'receptionist@candraresort.test')],
            [
                'name' => 'Receptionist Demo',
                'username' => 'receptionist',
                'employee_code' => 'REC-001',
                'phone' => '6281234567801',
                'role' => UserRole::Receptionist,
                'password' => $receptionistPassword ?: 'CandraReceptionist123',
                'is_active' => true,
                'created_by' => $owner->id,
            ]
        );

        foreach ($this->paymentMethods() as $method) {
            PaymentMethod::query()->updateOrCreate(
                ['code' => $method['code']],
                [...$method, 'created_by' => $owner->id]
            );
        }

        foreach ($this->hotelSettings() as $setting) {
            HotelSetting::query()->updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                [...$setting, 'updated_by' => $owner->id]
            );
        }

        foreach ($this->websiteContents() as $content) {
            WebsiteContent::query()->updateOrCreate(
                ['content_key' => $content['content_key']],
                [...$content, 'updated_by' => $owner->id]
            );
        }

        $facilities = collect($this->facilities())->mapWithKeys(function (array $facility): array {
            $model = Facility::query()->updateOrCreate(['slug' => $facility['slug']], $facility);

            return [$model->slug => $model];
        });

        foreach ($this->roomTypes() as $index => $roomTypeData) {
            $facilitySlugs = $roomTypeData['facilities'];
            unset($roomTypeData['facilities']);

            $roomType = RoomType::query()->updateOrCreate(['code' => $roomTypeData['code']], $roomTypeData);
            $roomType->facilities()->sync($facilities->only($facilitySlugs)->pluck('id'));

            foreach (range(1, 3) as $roomIndex) {
                Room::query()->firstOrCreate(
                    ['room_number' => (($index + 1) * 100) + $roomIndex],
                    [
                        'room_type_id' => $roomType->id,
                        'floor' => (string) ($index + 1),
                        'status' => RoomStatus::Available,
                        'is_active' => true,
                    ]
                );
            }
        }

        $deluxe = RoomType::query()->where('code', 'DLX')->first();
        if ($deluxe) {
            $promotion = Promotion::query()->updateOrCreate(
                ['code' => 'WELCOME10'],
                [
                    'name' => 'Welcome to Candra',
                    'description' => 'Diskon perkenalan untuk pengalaman menginap pertama di Candra Resort.',
                    'discount_type' => 'percent',
                    'discount_value' => 10,
                    'max_discount_amount' => 250000,
                    'minimum_transaction' => 750000,
                    'starts_at' => now()->startOfDay(),
                    'ends_at' => now()->addMonths(3)->endOfDay(),
                    'usage_quota' => 100,
                    'is_active' => true,
                    'created_by' => $owner->id,
                ]
            );
            $promotion->roomTypes()->syncWithoutDetaching([$deluxe->id]);
        }
    }

    private function paymentMethods(): array
    {
        return [
            ['name' => 'Midtrans', 'code' => 'midtrans', 'type' => 'gateway', 'channel' => 'midtrans', 'is_online' => true, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Cash', 'code' => 'cash', 'type' => 'cash', 'channel' => 'manual', 'is_online' => false, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Debit', 'code' => 'debit', 'type' => 'debit', 'channel' => 'manual', 'is_online' => false, 'is_active' => true, 'sort_order' => 3],
            ['name' => 'QRIS Manual', 'code' => 'qris-manual', 'type' => 'qris', 'channel' => 'manual', 'is_online' => false, 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Bank Transfer', 'code' => 'bank-transfer', 'type' => 'bank_transfer', 'channel' => 'manual', 'is_online' => false, 'is_active' => true, 'sort_order' => 5],
        ];
    }

    private function hotelSettings(): array
    {
        return [
            ['setting_group' => 'general', 'setting_key' => 'hotel.name', 'setting_value' => 'Candra Resort', 'value_type' => 'string', 'description' => 'Nama hotel'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.phone', 'setting_value' => '+62 812 3456 7890', 'value_type' => 'string', 'description' => 'Nomor telepon utama'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.email', 'setting_value' => 'info@candraresort.test', 'value_type' => 'string', 'description' => 'Email utama'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.address', 'setting_value' => 'Indonesia', 'value_type' => 'string', 'description' => 'Alamat hotel'],
            ['setting_group' => 'operation', 'setting_key' => 'hotel.check_in_time', 'setting_value' => '14:00', 'value_type' => 'time', 'description' => 'Waktu check-in standar'],
            ['setting_group' => 'operation', 'setting_key' => 'hotel.check_out_time', 'setting_value' => '12:00', 'value_type' => 'time', 'description' => 'Waktu check-out standar'],
        ];
    }

    private function websiteContents(): array
    {
        return [
            ['section' => 'hero', 'content_key' => 'hero_title', 'title' => 'A Warm Escape at Candra Resort', 'content' => null, 'sort_order' => 1, 'is_active' => true],
            ['section' => 'hero', 'content_key' => 'hero_description', 'title' => null, 'content' => 'Nikmati ketenangan, layanan yang hangat, dan pengalaman menginap yang dirancang untuk membuat setiap perjalanan lebih berkesan.', 'sort_order' => 2, 'is_active' => true],
            ['section' => 'about', 'content_key' => 'about_summary', 'title' => 'Tentang Candra Resort', 'content' => 'Candra Resort menghadirkan suasana nyaman untuk liburan keluarga, perjalanan bisnis, maupun waktu tenang bersama orang terdekat.', 'sort_order' => 1, 'is_active' => true],
        ];
    }

    private function facilities(): array
    {
        return [
            ['name' => 'Wi-Fi', 'slug' => 'wi-fi', 'scope' => 'both', 'icon' => 'flaticon-036-parking', 'description' => 'Internet tersedia di area hotel dan kamar.', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Restaurant', 'slug' => 'restaurant', 'scope' => 'hotel', 'icon' => 'flaticon-033-dinner', 'description' => 'Pilihan makanan dan minuman sepanjang hari.', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Housekeeping', 'slug' => 'housekeeping', 'scope' => 'both', 'icon' => 'flaticon-024-towel', 'description' => 'Layanan kebersihan untuk kenyamanan tamu.', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Air Conditioner', 'slug' => 'air-conditioner', 'scope' => 'room', 'icon' => 'flaticon-026-bed', 'description' => 'Pendingin ruangan di setiap kamar.', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Smart TV', 'slug' => 'smart-tv', 'scope' => 'room', 'icon' => 'flaticon-026-bed', 'description' => 'Hiburan di dalam kamar.', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Parking Area', 'slug' => 'parking-area', 'scope' => 'hotel', 'icon' => 'flaticon-036-parking', 'description' => 'Area parkir untuk tamu hotel.', 'is_active' => true, 'sort_order' => 6],
        ];
    }

    private function roomTypes(): array
    {
        return [
            ['code' => 'DLX', 'name' => 'Deluxe Room', 'slug' => 'deluxe-room', 'description' => 'Kamar modern dengan kenyamanan lengkap untuk dua tamu.', 'capacity' => 2, 'max_adults' => 2, 'max_children' => 1, 'bed_type' => 'King Bed', 'bed_count' => 1, 'room_size_sqm' => 30, 'base_price' => 750000, 'extra_bed_price' => 200000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 1, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv']],
            ['code' => 'FAM', 'name' => 'Family Room', 'slug' => 'family-room', 'description' => 'Ruang luas untuk menikmati perjalanan bersama keluarga.', 'capacity' => 4, 'max_adults' => 3, 'max_children' => 2, 'bed_type' => 'King & Single Bed', 'bed_count' => 2, 'room_size_sqm' => 42, 'base_price' => 1100000, 'extra_bed_price' => 200000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 2, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv']],
            ['code' => 'STE', 'name' => 'Candra Suite', 'slug' => 'candra-suite', 'description' => 'Suite premium dengan area duduk dan layanan terbaik Candra Resort.', 'capacity' => 3, 'max_adults' => 2, 'max_children' => 1, 'bed_type' => 'Super King Bed', 'bed_count' => 1, 'room_size_sqm' => 55, 'base_price' => 1650000, 'extra_bed_price' => 250000, 'breakfast_included' => true, 'is_active' => true, 'sort_order' => 3, 'facilities' => ['wi-fi', 'housekeeping', 'air-conditioner', 'smart-tv']],
        ];
    }
}
