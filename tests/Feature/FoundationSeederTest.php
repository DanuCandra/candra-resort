<?php

namespace Tests\Feature;

use App\Models\FoodCategory;
use App\Models\GalleryImage;
use App\Models\HotelService;
use App\Models\MenuItem;
use App\Models\PaymentMethod;
use App\Models\Promotion;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\FoundationSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FoundationSeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_foundation_seeder_creates_complete_data_and_is_safe_to_run_again(): void
    {
        Storage::fake('public');

        $this->seed(FoundationSeeder::class);
        $this->seed(FoundationSeeder::class);

        $owner = User::query()->where('employee_code', 'OWN-001')->firstOrFail();
        $receptionist = User::query()->where('employee_code', 'REC-001')->firstOrFail();

        $this->assertSame('danucahndx33@gmail.com', $owner->email);
        $this->assertSame('danucandraa100@gmail.com', $receptionist->email);
        $this->assertTrue(Hash::check('Password123', $owner->password));
        $this->assertTrue(Hash::check('Password123', $receptionist->password));
        $this->assertSame($owner->id, $receptionist->created_by);

        $this->assertSame(4, RoomType::query()->whereIn('code', ['DLX', 'PRM', 'FAM', 'STE'])->count());
        $this->assertSame(12, Room::query()->whereIn('room_number', ['101', '102', '103', '201', '202', '203', '301', '302', '303', '401', '402', '403'])->count());
        $this->assertSame(3, Promotion::query()->whereIn('code', ['WELCOME10', 'STAY3', 'FAMILY15'])->count());
        $this->assertSame(6, PaymentMethod::query()->whereIn('code', ['midtrans', 'cash', 'debit', 'qris-manual', 'bank-transfer', 'credit-card'])->count());
        $this->assertSame(4, FoodCategory::query()->whereIn('slug', ['sarapan', 'makanan-utama', 'camilan', 'minuman'])->count());
        $this->assertSame(8, MenuItem::query()->whereIn('slug', ['candra-breakfast', 'american-breakfast', 'nasi-goreng-candra', 'ayam-bakar-nusantara', 'grilled-chicken-steak', 'pisang-goreng-candra', 'fresh-tropical-juice', 'candra-coffee'])->count());
        $this->assertSame(6, HotelService::query()->whereIn('code', ['MASSAGE-60', 'SPA-PACKAGE', 'LAUNDRY-KG', 'EXTRA-BED', 'AIRPORT-PICKUP', 'AIRPORT-DROPOFF'])->count());
        $this->assertSame(4, GalleryImage::query()->where('image_path', 'like', 'seed/gallery/%')->count());
    }
}
