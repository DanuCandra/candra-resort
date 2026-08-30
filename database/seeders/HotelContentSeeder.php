<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\HotelSetting;
use App\Models\User;
use App\Models\WebsiteContent;
use App\Support\WebsiteContentRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HotelContentSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = User::query()->where('employee_code', 'OWN-001')->value('id');

        foreach ($this->settings() as $setting) {
            HotelSetting::query()->firstOrCreate(
                ['setting_key' => $setting['setting_key']],
                [...$setting, 'updated_by' => $ownerId]
            );
        }

        foreach ($this->contents() as $content) {
            $websiteContent = WebsiteContent::query()->withTrashed()->firstOrCreate(
                ['content_key' => $content['content_key']],
                [...$content, 'updated_by' => $ownerId]
            );
            $websiteContent->restore();
        }

        foreach (range(1, 4) as $index) {
            $path = "seed/gallery/gallery-{$index}.jpg";
            $this->copyPublicAsset("landing-lage/img/gallery/gallery-{$index}.jpg", $path);
            $galleryImage = GalleryImage::query()->withTrashed()->firstOrCreate(
                ['image_path' => $path],
                [
                    'caption' => ['Suasana Resort', 'Kenyamanan Kamar', 'Area Bersantai', 'Pengalaman Candra'][$index - 1],
                    'alt_text' => "Galeri Candra Resort {$index}",
                    'sort_order' => $index,
                    'is_active' => true,
                    'updated_by' => $ownerId,
                ]
            );
            $galleryImage->restore();
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function settings(): array
    {
        return [
            ['setting_group' => 'general', 'setting_key' => 'hotel.name', 'setting_value' => 'Candra Resort', 'value_type' => 'string', 'description' => 'Nama hotel'],
            ['setting_group' => 'general', 'setting_key' => 'hotel.tagline', 'setting_value' => 'Hotel & Experience', 'value_type' => 'string', 'description' => 'Tagline singkat hotel'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.phone', 'setting_value' => '+62 812 3456 7890', 'value_type' => 'string', 'description' => 'Nomor telepon utama'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.whatsapp', 'setting_value' => '+62 812 3456 7890', 'value_type' => 'string', 'description' => 'Nomor WhatsApp reservasi'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.email', 'setting_value' => 'info@candraresort.test', 'value_type' => 'string', 'description' => 'Email utama'],
            ['setting_group' => 'contact', 'setting_key' => 'hotel.address', 'setting_value' => 'Indonesia', 'value_type' => 'string', 'description' => 'Alamat hotel'],
            ['setting_group' => 'operation', 'setting_key' => 'hotel.check_in_time', 'setting_value' => '14:00', 'value_type' => 'time', 'description' => 'Waktu check-in standar'],
            ['setting_group' => 'operation', 'setting_key' => 'hotel.check_out_time', 'setting_value' => '12:00', 'value_type' => 'time', 'description' => 'Waktu check-out standar'],
            ['setting_group' => 'operation', 'setting_key' => 'hotel.currency', 'setting_value' => 'IDR', 'value_type' => 'string', 'description' => 'Mata uang transaksi'],
            ['setting_group' => 'social', 'setting_key' => 'social.instagram', 'setting_value' => null, 'value_type' => 'string', 'description' => 'Tautan Instagram'],
            ['setting_group' => 'social', 'setting_key' => 'social.facebook', 'setting_value' => null, 'value_type' => 'string', 'description' => 'Tautan Facebook'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function contents(): array
    {
        return collect(WebsiteContentRegistry::slots())
            ->map(fn (array $slot, string $key): array => [
                'section' => $slot['section'],
                'content_key' => $key,
                'title' => $slot['defaultTitle'],
                'content' => $slot['defaultContent'],
                'sort_order' => $slot['sortOrder'],
                'is_active' => true,
            ])
            ->values()
            ->all();
    }

    private function copyPublicAsset(string $source, string $destination): void
    {
        $sourcePath = public_path($source);
        if (is_file($sourcePath) && ! Storage::disk('public')->exists($destination)) {
            Storage::disk('public')->put($destination, file_get_contents($sourcePath));
        }
    }
}
