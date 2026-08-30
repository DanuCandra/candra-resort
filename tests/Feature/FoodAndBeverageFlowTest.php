<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\FoodCategory;
use App\Models\FoodOrder;
use App\Models\GuestRoomAccess;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class FoodAndBeverageFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receptionist_can_manage_food_categories_and_menu_items(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->post(route('receptionist.food-categories.store'), [
            'name' => 'Main Course',
            'description' => 'Menu utama',
            'sort_order' => 1,
            'is_active' => 1,
        ])->assertRedirect(route('receptionist.food-categories.index'));

        $category = FoodCategory::where('slug', 'main-course')->firstOrFail();
        $this->actingAs($receptionist)->post(route('receptionist.menu-items.store'), [
            'food_category_id' => $category->id,
            'name' => 'Nasi Goreng Candra',
            'description' => 'Nasi goreng khas resort',
            'price' => 85000,
            'preparation_minutes' => 20,
            'is_available' => 1,
            'is_active' => 1,
        ])->assertRedirect(route('receptionist.menu-items.index'));

        $this->assertDatabaseHas('menu_items', [
            'food_category_id' => $category->id,
            'slug' => 'nasi-goreng-candra',
            'price' => 85000,
            'is_available' => true,
        ]);
    }

    public function test_receptionist_menu_create_page_renders_without_blade_error(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)
            ->get(route('receptionist.menu-items.create'))
            ->assertOk()
            ->assertSee('Tambah Menu')
            ->assertSee('Foto menu');
    }

    public function test_verified_room_guest_can_order_with_price_and_name_snapshots(): void
    {
        [$access, $plainToken] = $this->activeAccess();
        $item = $this->menuItem(75000);

        $response = $this->withSession(['room_service_access_token' => $plainToken])
            ->post(route('room-service.food.store'), [
                'items' => [$item->id => ['quantity' => 2, 'special_notes' => 'Tidak pedas']],
                'delivery_notes' => 'Antar ke pintu kamar',
            ]);

        $order = FoodOrder::where('stay_id', $access->stay_id)->firstOrFail();
        $response->assertRedirect(route('room-service.food.show', $order));
        $this->assertSame('requested', $order->status->value);
        $this->assertSame(150000.0, (float) $order->total_amount);
        $this->assertDatabaseHas('food_order_items', [
            'food_order_id' => $order->id,
            'item_name' => $item->name,
            'quantity' => 2,
            'unit_price' => 75000,
            'subtotal' => 150000,
            'special_notes' => 'Tidak pedas',
        ]);
    }

    public function test_completed_order_is_posted_once_to_folio_after_valid_transitions(): void
    {
        [$access, $plainToken] = $this->activeAccess();
        $item = $this->menuItem(90000);
        $receptionist = User::factory()->receptionist()->create();
        $this->withSession(['room_service_access_token' => $plainToken])->post(route('room-service.food.store'), [
            'items' => [$item->id => ['quantity' => 1]],
        ]);
        $order = FoodOrder::where('stay_id', $access->stay_id)->firstOrFail();

        foreach (['accepted', 'processing', 'completed'] as $status) {
            $this->actingAs($receptionist)->post(route('receptionist.food-orders.status', $order), ['status' => $status])->assertSessionHasNoErrors();
            $order->refresh();
            $this->assertSame($status, $order->status->value);
        }

        $folio = Folio::where('stay_id', $access->stay_id)->firstOrFail();
        $this->assertDatabaseHas('folio_items', [
            'folio_id' => $folio->id,
            'item_type' => 'food',
            'source_id' => $order->id,
            'amount' => 90000,
        ]);
        $this->assertSame(590000.0, (float) $folio->fresh()->total_amount);
        $this->assertSame(590000.0, (float) $folio->fresh()->balance_amount);

        $this->actingAs($receptionist)->from(route('receptionist.food-orders.show', $order))
            ->post(route('receptionist.food-orders.status', $order), ['status' => 'completed'])
            ->assertRedirect(route('receptionist.food-orders.show', $order))->assertSessionHasErrors('status');
        $this->assertSame(1, $folio->items()->where('source_id', $order->id)->count());
    }

    public function test_room_guest_cannot_view_another_stays_food_order(): void
    {
        [$firstAccess] = $this->activeAccess();
        [, $secondToken] = $this->activeAccess();
        $foreignOrder = FoodOrder::create([
            'order_code' => 'FNB-'.Str::upper((string) Str::ulid()),
            'stay_id' => $firstAccess->stay_id,
            'room_id' => $firstAccess->room_id,
            'status' => 'requested',
            'subtotal' => 10000,
            'total_amount' => 10000,
            'charge_to_room' => true,
        ]);

        $this->withSession(['room_service_access_token' => $secondToken])
            ->get(route('room-service.food.show', $foreignOrder))->assertNotFound();
    }

    private function menuItem(int $price): MenuItem
    {
        $category = FoodCategory::create([
            'name' => 'Kategori '.Str::random(6),
            'slug' => 'kategori-'.Str::lower(Str::random(10)),
            'is_active' => true,
        ]);

        return MenuItem::create([
            'food_category_id' => $category->id,
            'name' => 'Menu '.Str::random(6),
            'slug' => 'menu-'.Str::lower(Str::random(10)),
            'price' => $price,
            'is_available' => true,
            'is_active' => true,
        ]);
    }

    private function activeAccess(): array
    {
        $roomType = RoomType::create([
            'code' => 'FNB-'.Str::upper(Str::random(6)),
            'name' => 'F&B Room '.Str::random(6),
            'slug' => 'fnb-room-'.Str::lower(Str::random(10)),
            'capacity' => 2,
            'max_adults' => 2,
            'max_children' => 0,
            'bed_count' => 1,
            'base_price' => 500000,
            'extra_bed_price' => 0,
            'is_active' => true,
        ]);
        $room = Room::create([
            'room_type_id' => $roomType->id,
            'room_number' => 'FNB-'.Str::upper(Str::random(6)),
            'status' => 'occupied',
            'is_active' => true,
        ]);
        $reservation = Reservation::create([
            'booking_code' => 'FNB-BOOK-'.Str::upper(Str::random(8)),
            'room_type_id' => $roomType->id,
            'room_id' => $room->id,
            'source' => 'walk_in',
            'guest_name' => 'F&B Guest',
            'guest_phone' => '628123456789',
            'check_in_date' => today(),
            'check_out_date' => today()->addDay(),
            'total_nights' => 1,
            'adults' => 1,
            'status' => 'checked_in',
            'payment_status' => 'unpaid',
            'currency' => 'IDR',
            'subtotal' => 500000,
            'grand_total' => 500000,
        ]);
        $stay = Stay::create([
            'reservation_id' => $reservation->id,
            'room_id' => $room->id,
            'guest_name' => 'F&B Guest',
            'guest_phone' => '628123456789',
            'check_in_at' => now(),
            'status' => 'active',
        ]);
        $folio = Folio::create([
            'folio_number' => 'FOL-'.Str::upper((string) Str::ulid()),
            'stay_id' => $stay->id,
            'reservation_id' => $reservation->id,
            'status' => 'open',
            'currency' => 'IDR',
            'subtotal' => 500000,
            'total_amount' => 500000,
            'balance_amount' => 500000,
        ]);
        $folio->items()->create([
            'item_type' => 'room',
            'description' => 'Room charge',
            'quantity' => 1,
            'unit_price' => 500000,
            'amount' => 500000,
            'posted_at' => now(),
        ]);
        $plainToken = Str::random(80);
        $access = GuestRoomAccess::create([
            'stay_id' => $stay->id,
            'room_id' => $room->id,
            'session_token' => hash('sha256', $plainToken),
            'phone_verified_at' => now(),
            'expires_at' => today()->addDay()->endOfDay(),
        ]);

        return [$access, $plainToken];
    }
}
