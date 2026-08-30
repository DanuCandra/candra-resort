<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FoodBeverageSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Sarapan', 'slug' => 'sarapan', 'description' => 'Pilihan sarapan untuk memulai hari.', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Makanan Utama', 'slug' => 'makanan-utama', 'description' => 'Hidangan utama khas Indonesia dan internasional.', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Camilan', 'slug' => 'camilan', 'description' => 'Camilan ringan untuk menemani waktu bersantai.', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Minuman dingin dan hangat.', 'is_active' => true, 'sort_order' => 4],
        ])->mapWithKeys(function (array $data): array {
            $category = FoodCategory::query()->withTrashed()->updateOrCreate(['slug' => $data['slug']], $data);
            $category->restore();

            return [$category->slug => $category];
        });

        foreach ($this->menuItems() as $index => $data) {
            $categorySlug = $data['category'];
            unset($data['category']);
            $imagePath = 'seed/menu/menu-'.($index + 1).'.jpg';
            $sourceIndex = ($index % 3) + 1;
            $this->copyPublicAsset("landing-lage/img/blog/blog-{$sourceIndex}.jpg", $imagePath);
            $item = MenuItem::query()->withTrashed()->updateOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'food_category_id' => $categories[$categorySlug]->id, 'image_path' => $imagePath]
            );
            $item->restore();
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function menuItems(): array
    {
        return [
            ['category' => 'sarapan', 'name' => 'Candra Breakfast', 'slug' => 'candra-breakfast', 'description' => 'Nasi goreng, telur, buah segar, dan pilihan kopi atau teh.', 'price' => 85000, 'preparation_minutes' => 20, 'is_available' => true, 'is_active' => true, 'sort_order' => 1],
            ['category' => 'sarapan', 'name' => 'American Breakfast', 'slug' => 'american-breakfast', 'description' => 'Toast, sosis, telur, salad, dan jus buah.', 'price' => 95000, 'preparation_minutes' => 20, 'is_available' => true, 'is_active' => true, 'sort_order' => 2],
            ['category' => 'makanan-utama', 'name' => 'Nasi Goreng Candra', 'slug' => 'nasi-goreng-candra', 'description' => 'Nasi goreng spesial dengan ayam, telur, acar, dan kerupuk.', 'price' => 75000, 'preparation_minutes' => 25, 'is_available' => true, 'is_active' => true, 'sort_order' => 3],
            ['category' => 'makanan-utama', 'name' => 'Ayam Bakar Nusantara', 'slug' => 'ayam-bakar-nusantara', 'description' => 'Ayam bakar berbumbu dengan nasi hangat dan sambal.', 'price' => 90000, 'preparation_minutes' => 30, 'is_available' => true, 'is_active' => true, 'sort_order' => 4],
            ['category' => 'makanan-utama', 'name' => 'Grilled Chicken Steak', 'slug' => 'grilled-chicken-steak', 'description' => 'Dada ayam panggang dengan kentang dan sayuran.', 'price' => 120000, 'preparation_minutes' => 30, 'is_available' => true, 'is_active' => true, 'sort_order' => 5],
            ['category' => 'camilan', 'name' => 'Pisang Goreng Candra', 'slug' => 'pisang-goreng-candra', 'description' => 'Pisang goreng renyah dengan keju dan cokelat.', 'price' => 45000, 'preparation_minutes' => 15, 'is_available' => true, 'is_active' => true, 'sort_order' => 6],
            ['category' => 'minuman', 'name' => 'Fresh Tropical Juice', 'slug' => 'fresh-tropical-juice', 'description' => 'Pilihan jus buah tropis segar.', 'price' => 40000, 'preparation_minutes' => 10, 'is_available' => true, 'is_active' => true, 'sort_order' => 7],
            ['category' => 'minuman', 'name' => 'Candra Coffee', 'slug' => 'candra-coffee', 'description' => 'Kopi pilihan yang disajikan panas atau dingin.', 'price' => 38000, 'preparation_minutes' => 10, 'is_available' => true, 'is_active' => true, 'sort_order' => 8],
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
