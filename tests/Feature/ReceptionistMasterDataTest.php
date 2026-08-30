<?php

namespace Tests\Feature;

use App\Enums\RoomStatus;
use App\Models\Facility;
use App\Models\GuestRoomAccess;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Promotion;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReceptionistMasterDataTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receptionist_can_create_and_update_a_facility(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->post(route('receptionist.facilities.store'), [
            'name' => 'Private Test Pool',
            'scope' => 'both',
            'description' => 'Fasilitas khusus pengujian.',
            'sort_order' => 8,
            'is_active' => 1,
        ])->assertRedirect(route('receptionist.facilities.index'));

        $facility = Facility::where('slug', 'private-test-pool')->firstOrFail();
        $this->actingAs($receptionist)->put(route('receptionist.facilities.update', $facility), [
            'name' => 'Private Test Pool Updated',
            'scope' => 'hotel',
            'sort_order' => 9,
            'is_active' => 1,
        ])->assertRedirect(route('receptionist.facilities.index'));

        $this->assertDatabaseHas('facilities', ['id' => $facility->id, 'scope' => 'hotel', 'sort_order' => 9]);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $facility->id, 'module' => 'facilities', 'event' => 'update']);
    }

    public function test_receptionist_can_create_room_type_with_facility_and_image(): void
    {
        Storage::fake('public');
        $receptionist = User::factory()->receptionist()->create();
        $facility = Facility::create(['name' => 'Test WiFi', 'slug' => 'test-wifi', 'scope' => 'room', 'is_active' => true]);

        $response = $this->actingAs($receptionist)->post(route('receptionist.room-types.store'), [
            'code' => 'TST-DLX',
            'name' => 'Test Deluxe',
            'description' => 'Kamar untuk pengujian.',
            'capacity' => 3,
            'max_adults' => 2,
            'max_children' => 1,
            'bed_type' => 'King',
            'bed_count' => 1,
            'room_size_sqm' => 32,
            'base_price' => 850000,
            'extra_bed_price' => 150000,
            'breakfast_included' => 1,
            'is_active' => 1,
            'facilities' => [$facility->id],
            'images' => [UploadedFile::fake()->image('deluxe.jpg', 1200, 800)],
        ]);

        $roomType = RoomType::where('code', 'TST-DLX')->firstOrFail();
        $response->assertRedirect(route('receptionist.room-types.show', $roomType));
        $this->assertTrue($roomType->facilities()->whereKey($facility->id)->exists());
        $image = $roomType->images()->firstOrFail();
        $this->assertTrue($image->is_primary);
        Storage::disk('public')->assertExists($image->image_path);
    }

    public function test_room_creation_records_status_and_generates_working_qr_png(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $roomType = $this->roomType();

        $response = $this->actingAs($receptionist)->post(route('receptionist.rooms.store'), [
            'room_type_id' => $roomType->id,
            'room_number' => 'T-501',
            'floor' => '5',
            'status' => RoomStatus::Available->value,
            'is_active' => 1,
        ]);

        $room = Room::where('room_number', 'T-501')->firstOrFail();
        $response->assertRedirect(route('receptionist.rooms.show', $room));
        $this->assertTrue(Str::isUuid($room->qr_token));
        $this->assertDatabaseHas('room_status_histories', ['room_id' => $room->id, 'new_status' => 'available']);

        $qrResponse = $this->actingAs($receptionist)->get(route('receptionist.rooms.qr.image', $room));
        $qrResponse->assertOk()->assertHeader('content-type', 'image/png');
        $this->assertStringStartsWith("\x89PNG", $qrResponse->getContent());

        $oldToken = $room->qr_token;
        $this->actingAs($receptionist)->post(route('receptionist.rooms.qr.regenerate', $room))->assertRedirect();
        $this->assertNotSame($oldToken, $room->fresh()->qr_token);
    }

    public function test_qr_pages_repair_a_missing_token_for_legacy_room(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $room = Room::create([
            'room_type_id' => $this->roomType()->id,
            'room_number' => 'LEGACY-QR-01',
            'status' => RoomStatus::Available,
            'is_active' => true,
        ]);
        $room->forceFill(['qr_token' => null])->saveQuietly();

        $this->actingAs($receptionist)->get(route('receptionist.rooms.qr', $room))
            ->assertOk()->assertSee('QR Kamar LEGACY-QR-01');

        $room->refresh();
        $this->assertTrue(Str::isUuid($room->qr_token));
        $this->actingAs($receptionist)->get(route('receptionist.rooms.qr.print', $room))
            ->assertOk()->assertSee('KAMAR LEGACY-QR-01');
        $this->actingAs($receptionist)->get(route('receptionist.rooms.qr.image', $room))
            ->assertOk()->assertHeader('content-type', 'image/png');
    }

    public function test_receptionist_can_create_rate_promotion_and_payment_method(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $roomType = $this->roomType();

        $this->actingAs($receptionist)->post(route('receptionist.pricing.store'), [
            'room_type_id' => $roomType->id,
            'name' => 'Test Weekend',
            'days_of_week' => [6, 7],
            'price_per_night' => 975000,
            'priority' => 10,
            'is_active' => 1,
        ])->assertRedirect(route('receptionist.pricing.index'));

        $this->actingAs($receptionist)->post(route('receptionist.promotions.store'), [
            'code' => 'TEST20',
            'name' => 'Test Promotion',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'minimum_transaction' => 500000,
            'is_active' => 1,
            'room_types' => [$roomType->id],
        ])->assertRedirect(route('receptionist.promotions.index'));

        $this->actingAs($receptionist)->post(route('receptionist.payment-methods.store'), [
            'name' => 'Test Manual QRIS',
            'type' => 'qris',
            'channel' => 'manual',
            'sort_order' => 7,
            'is_active' => 1,
        ])->assertRedirect(route('receptionist.payment-methods.index'));

        $this->assertDatabaseHas('room_rates', ['room_type_id' => $roomType->id, 'name' => 'Test Weekend']);
        $promotion = Promotion::where('code', 'TEST20')->firstOrFail();
        $this->assertTrue($promotion->roomTypes()->whereKey($roomType->id)->exists());
        $this->assertDatabaseHas('payment_methods', ['code' => 'test_manual_qris', 'is_online' => false]);
    }

    public function test_used_payment_method_is_deactivated_instead_of_deleted(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $method = PaymentMethod::create([
            'name' => 'Test Cash Used',
            'code' => 'test_cash_used',
            'type' => 'cash',
            'channel' => 'manual',
            'is_online' => false,
            'is_active' => true,
        ]);
        Payment::create([
            'payment_code' => 'PAY-'.Str::upper(Str::random(12)),
            'payment_method_id' => $method->id,
            'purpose' => 'other',
            'status' => 'paid',
            'source' => 'receptionist',
            'currency' => 'IDR',
            'amount' => 100000,
        ]);

        $this->actingAs($receptionist)->delete(route('receptionist.payment-methods.destroy', $method))->assertRedirect();

        $this->assertDatabaseHas('payment_methods', ['id' => $method->id, 'is_active' => false, 'deleted_at' => null]);
    }

    public function test_owner_cannot_access_receptionist_master_data(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get(route('receptionist.rooms.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('receptionist.payment-methods.index'))->assertForbidden();
    }

    public function test_room_service_requires_active_stay_and_matching_phone(): void
    {
        $room = Room::create([
            'room_type_id' => $this->roomType()->id,
            'room_number' => 'RS-701',
            'status' => 'occupied',
            'is_active' => true,
        ]);

        $this->get(route('room-service.verify', $room->qr_token))
            ->assertOk()->assertSee('Tidak ada masa menginap aktif');

        $stay = $this->activeStay($room, '628123456789');

        $this->from(route('room-service.verify', $room->qr_token))
            ->post(route('room-service.verify.store', $room->qr_token), ['phone' => '0812-0000-0000'])
            ->assertSessionHasErrors('phone');

        $this->post(route('room-service.verify.store', $room->qr_token), ['phone' => '0812-3456-789'])
            ->assertRedirect(route('room-service.home'));

        $access = GuestRoomAccess::where('stay_id', $stay->id)->firstOrFail();
        $plainToken = session('room_service_access_token');
        $this->assertNotSame($plainToken, $access->session_token);
        $this->assertSame(hash('sha256', $plainToken), $access->session_token);
        $this->get(route('room-service.home'))->assertOk()->assertSee('Kamar RS-701');
    }

    public function test_regenerating_room_qr_revokes_existing_room_service_access(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $room = Room::create([
            'room_type_id' => $this->roomType()->id,
            'room_number' => 'RS-702',
            'status' => 'occupied',
            'is_active' => true,
        ]);
        $stay = $this->activeStay($room, '628111222333');
        $access = GuestRoomAccess::create([
            'stay_id' => $stay->id,
            'room_id' => $room->id,
            'session_token' => hash('sha256', Str::random(80)),
            'phone_verified_at' => now(),
        ]);

        $this->actingAs($receptionist)->post(route('receptionist.rooms.qr.regenerate', $room))->assertRedirect();

        $this->assertNotNull($access->fresh()->revoked_at);
    }

    private function roomType(): RoomType
    {
        return RoomType::create([
            'code' => 'RT-'.Str::upper(Str::random(8)),
            'name' => 'Room Type '.Str::random(8),
            'slug' => 'room-type-'.Str::lower(Str::random(12)),
            'capacity' => 2,
            'max_adults' => 2,
            'max_children' => 0,
            'bed_count' => 1,
            'base_price' => 700000,
            'extra_bed_price' => 0,
            'is_active' => true,
        ]);
    }

    private function activeStay(Room $room, string $phone): Stay
    {
        $reservation = Reservation::create([
            'booking_code' => 'BOOK-'.Str::upper(Str::random(10)),
            'room_type_id' => $room->room_type_id,
            'room_id' => $room->id,
            'source' => 'online',
            'guest_name' => 'Room Service Guest',
            'guest_phone' => $phone,
            'check_in_date' => today(),
            'check_out_date' => today()->addDays(2),
            'total_nights' => 2,
            'adults' => 1,
            'status' => 'checked_in',
            'payment_status' => 'paid',
            'currency' => 'IDR',
            'subtotal' => 1400000,
            'grand_total' => 1400000,
        ]);

        return Stay::create([
            'reservation_id' => $reservation->id,
            'room_id' => $room->id,
            'guest_name' => 'Room Service Guest',
            'guest_phone' => $phone,
            'check_in_at' => now(),
            'status' => 'active',
        ]);
    }
}
