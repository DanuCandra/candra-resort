<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\GuestRequest;
use App\Models\GuestRoomAccess;
use App\Models\HotelService;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\ServiceOrder;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class OperationalGuestServicesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receptionist_can_create_hotel_service(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->post(route('receptionist.hotel-services.store'), [
            'name' => 'Balinese Massage', 'category' => 'massage', 'price' => 350000,
            'price_unit' => 'per_hour', 'duration_minutes' => 60, 'requires_schedule' => 1,
            'is_available' => 1, 'is_active' => 1,
        ])->assertRedirect(route('receptionist.hotel-services.index'));

        $this->assertDatabaseHas('hotel_services', [
            'code' => 'BALINESE-MASSAGE', 'price' => 350000, 'requires_schedule' => true,
        ]);
    }

    public function test_room_guest_can_order_scheduled_service_with_price_snapshot(): void
    {
        [$access, $token] = $this->activeAccess();
        $service = $this->hotelService(true, 200000);
        $schedule = now()->addDay()->startOfHour();

        $this->withSession(['room_service_access_token' => $token])->post(route('room-service.services.store'), [
            'hotel_service_id' => $service->id, 'quantity' => 2, 'scheduled_at' => $schedule->format('Y-m-d H:i:s'),
            'notes' => 'Dua orang',
        ])->assertRedirect();

        $order = ServiceOrder::where('stay_id', $access->stay_id)->firstOrFail();
        $this->assertSame('requested', $order->status->value);
        $this->assertSame(400000.0, (float) $order->total_amount);
        $this->assertSame(200000.0, (float) $order->unit_price);
        $this->assertSame('Dua orang', $order->notes);
    }

    public function test_completed_service_is_added_once_to_folio(): void
    {
        [$access, $token] = $this->activeAccess();
        $service = $this->hotelService(false, 125000);
        $receptionist = User::factory()->receptionist()->create();
        $this->withSession(['room_service_access_token' => $token])->post(route('room-service.services.store'), [
            'hotel_service_id' => $service->id, 'quantity' => 1,
        ]);
        $order = ServiceOrder::where('stay_id', $access->stay_id)->firstOrFail();

        foreach (['accepted', 'processing', 'completed'] as $status) {
            $this->actingAs($receptionist)->post(route('receptionist.service-orders.status', $order), ['status' => $status])->assertSessionHasNoErrors();
            $order->refresh();
        }

        $folio = Folio::where('stay_id', $access->stay_id)->firstOrFail();
        $this->assertSame('completed', $order->status->value);
        $this->assertDatabaseHas('folio_items', ['folio_id' => $folio->id, 'item_type' => 'service', 'source_id' => $order->id, 'amount' => 125000]);
        $this->assertSame(625000.0, (float) $folio->fresh()->balance_amount);
        $this->assertSame(1, $folio->items()->where('source_type', $order->getMorphClass())->where('source_id', $order->id)->count());
    }

    public function test_guest_request_follows_operational_status_flow(): void
    {
        [$access, $token] = $this->activeAccess();
        $receptionist = User::factory()->receptionist()->create();

        $this->withSession(['room_service_access_token' => $token])->post(route('room-service.requests.store'), [
            'type' => 'housekeeping', 'title' => 'Pembersihan Kamar',
            'description' => 'Mohon dibersihkan setelah pukul 14.00', 'priority' => 'normal',
        ])->assertRedirect();
        $guestRequest = GuestRequest::where('stay_id', $access->stay_id)->firstOrFail();

        foreach (['accepted', 'processing', 'completed'] as $status) {
            $this->actingAs($receptionist)->post(route('receptionist.guest-requests.status', $guestRequest), ['status' => $status])->assertSessionHasNoErrors();
            $guestRequest->refresh();
        }

        $this->assertSame('completed', $guestRequest->status->value);
        $this->assertNotNull($guestRequest->completed_at);
        $this->assertSame($receptionist->id, $guestRequest->handled_by);
    }

    private function hotelService(bool $schedule, int $price): HotelService
    {
        return HotelService::create([
            'code' => 'SVC-'.Str::upper(Str::random(8)), 'name' => 'Service '.Str::random(6),
            'category' => 'other', 'price' => $price, 'price_unit' => 'per_order',
            'requires_schedule' => $schedule, 'is_available' => true, 'is_active' => true,
        ]);
    }

    private function activeAccess(): array
    {
        $roomType = RoomType::create([
            'code' => 'GSV-'.Str::upper(Str::random(6)), 'name' => 'Guest Service Room '.Str::random(5),
            'slug' => 'guest-service-'.Str::lower(Str::random(10)), 'capacity' => 2, 'max_adults' => 2,
            'max_children' => 0, 'bed_count' => 1, 'base_price' => 500000, 'extra_bed_price' => 0, 'is_active' => true,
        ]);
        $room = Room::create(['room_type_id' => $roomType->id, 'room_number' => 'GSV-'.Str::upper(Str::random(5)), 'status' => 'occupied', 'is_active' => true]);
        $reservation = Reservation::create([
            'booking_code' => 'GSV-BOOK-'.Str::upper(Str::random(7)), 'room_type_id' => $roomType->id, 'room_id' => $room->id,
            'source' => 'walk_in', 'guest_name' => 'Service Guest', 'guest_phone' => '628123456789',
            'check_in_date' => today(), 'check_out_date' => today()->addDays(2), 'total_nights' => 2,
            'adults' => 1, 'status' => 'checked_in', 'payment_status' => 'unpaid', 'currency' => 'IDR',
            'subtotal' => 500000, 'grand_total' => 500000,
        ]);
        $stay = Stay::create([
            'reservation_id' => $reservation->id, 'room_id' => $room->id, 'guest_name' => 'Service Guest',
            'guest_phone' => '628123456789', 'check_in_at' => now(), 'status' => 'active',
        ]);
        $folio = Folio::create([
            'folio_number' => 'FOL-'.Str::upper((string) Str::ulid()), 'stay_id' => $stay->id,
            'reservation_id' => $reservation->id, 'status' => 'open', 'currency' => 'IDR',
            'subtotal' => 500000, 'total_amount' => 500000, 'balance_amount' => 500000,
        ]);
        $folio->items()->create(['item_type' => 'room', 'description' => 'Room charge', 'quantity' => 1, 'unit_price' => 500000, 'amount' => 500000, 'posted_at' => now()]);
        $token = Str::random(80);
        $access = GuestRoomAccess::create([
            'stay_id' => $stay->id, 'room_id' => $room->id, 'session_token' => hash('sha256', $token),
            'phone_verified_at' => now(), 'expires_at' => today()->addDays(2)->endOfDay(),
        ]);

        return [$access, $token];
    }
}
