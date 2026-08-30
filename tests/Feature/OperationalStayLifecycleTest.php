<?php

namespace Tests\Feature;

use App\Models\GuestRoomAccess;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\ReservationNight;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class OperationalStayLifecycleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receptionist_can_create_walk_in_with_manual_payment(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        [$roomType, $room] = $this->room();
        $method = $this->manualMethod();

        $response = $this->actingAs($receptionist)->post(route('receptionist.reservations.walk-in.store'), [
            'guest_name' => 'Walk In Guest',
            'guest_phone' => '0812-3456-7890',
            'guest_email' => 'walkin@example.com',
            'room_id' => $room->id,
            'check_in' => today()->toDateString(),
            'check_out' => today()->addDay()->toDateString(),
            'adults' => 2,
            'children' => 0,
            'payment_amount' => 300000,
            'payment_method_id' => $method->id,
        ]);

        $reservation = Reservation::where('guest_email', 'walkin@example.com')->firstOrFail();
        $response->assertRedirect(route('receptionist.reservations.show', $reservation));
        $this->assertSame($roomType->id, $reservation->room_type_id);
        $this->assertSame('walk_in', $reservation->source);
        $this->assertSame('confirmed', $reservation->status->value);
        $this->assertSame('partial', $reservation->payment_status->value);
        $this->assertSame('6281234567890', $reservation->guest_phone);
        $this->assertDatabaseHas('payments', ['reservation_id' => $reservation->id, 'amount' => 300000, 'status' => 'paid']);
        $this->assertSame('reserved', $room->fresh()->status->value);
    }

    public function test_check_in_creates_private_identity_stay_folio_and_occupied_room(): void
    {
        Storage::fake('local');
        $receptionist = User::factory()->receptionist()->create();
        [, $room] = $this->room();
        $reservation = $this->confirmedReservation($room);
        $method = $this->manualMethod();

        $response = $this->actingAs($receptionist)->post(route('receptionist.checkin.store', $reservation), [
            'room_id' => $room->id,
            'guest_phone' => '0812 1111 2222',
            'identity_type' => 'KTP',
            'identity_number' => '3174000012345678',
            'identity_photo' => UploadedFile::fake()->image('ktp.jpg', 1000, 640),
            'key_code' => 'KEY-101',
            'security_deposit_amount' => 100000,
            'payment_amount' => 200000,
            'payment_method_id' => $method->id,
            'confirm_check_in' => 1,
        ]);

        $response->assertRedirect(route('receptionist.reservations.show', $reservation));
        $stay = Stay::where('reservation_id', $reservation->id)->firstOrFail();
        $this->assertSame('active', $stay->status->value);
        $this->assertSame('6281211112222', $stay->guest_phone);
        $this->assertSame('3174000012345678', $stay->identity_number);
        $rawIdentity = DB::table('stays')->where('id', $stay->id)->value('identity_number');
        $this->assertNotSame('3174000012345678', $rawIdentity);
        Storage::disk('local')->assertExists($stay->identity_photo_path);
        $this->assertSame('checked_in', $reservation->fresh()->status->value);
        $this->assertSame('occupied', $room->fresh()->status->value);
        $this->assertDatabaseHas('folios', ['stay_id' => $stay->id, 'status' => 'open', 'total_amount' => 700000, 'paid_amount' => 200000, 'balance_amount' => 500000]);
        $this->assertDatabaseHas('folio_items', ['folio_id' => $stay->folio->id, 'item_type' => 'room', 'amount' => 700000]);
    }

    public function test_check_out_collects_balance_revokes_qr_and_marks_room_cleaning(): void
    {
        Storage::fake('local');
        $receptionist = User::factory()->receptionist()->create();
        [, $room] = $this->room();
        $reservation = $this->confirmedReservation($room);
        $method = $this->manualMethod();
        $this->actingAs($receptionist)->post(route('receptionist.checkin.store', $reservation), [
            'room_id' => $room->id,
            'guest_phone' => '081299998888',
            'identity_type' => 'KTP',
            'identity_photo' => UploadedFile::fake()->image('identity.jpg'),
            'key_code' => 'KEY-CHECKOUT',
            'payment_amount' => 200000,
            'payment_method_id' => $method->id,
            'confirm_check_in' => 1,
        ]);
        $stay = Stay::where('reservation_id', $reservation->id)->firstOrFail();
        $access = GuestRoomAccess::create([
            'stay_id' => $stay->id,
            'room_id' => $room->id,
            'session_token' => hash('sha256', Str::random(80)),
            'phone_verified_at' => now(),
        ]);

        $response = $this->actingAs($receptionist)->post(route('receptionist.checkout.store', $stay), [
            'payment_amount' => 500000,
            'payment_method_id' => $method->id,
            'key_returned' => 1,
            'confirm_check_out' => 1,
        ]);

        $response->assertRedirect(route('receptionist.reservations.show', $reservation));
        $this->assertSame('completed', $stay->fresh()->status->value);
        $this->assertNotNull($stay->fresh()->key_returned_at);
        $this->assertSame('checked_out', $reservation->fresh()->status->value);
        $this->assertSame('cleaning', $room->fresh()->status->value);
        $this->assertNotNull($access->fresh()->revoked_at);
        $this->assertDatabaseHas('folios', ['stay_id' => $stay->id, 'status' => 'closed', 'paid_amount' => 700000, 'balance_amount' => 0]);
        $this->assertDatabaseHas('room_status_histories', ['room_id' => $room->id, 'new_status' => 'cleaning']);
    }

    public function test_check_out_rejects_incorrect_final_payment_without_partial_updates(): void
    {
        Storage::fake('local');
        $receptionist = User::factory()->receptionist()->create();
        [, $room] = $this->room();
        $reservation = $this->confirmedReservation($room);
        $method = $this->manualMethod();
        $this->actingAs($receptionist)->post(route('receptionist.checkin.store', $reservation), [
            'room_id' => $room->id,
            'guest_phone' => '081277766655',
            'identity_type' => 'KTP',
            'identity_photo' => UploadedFile::fake()->image('identity.jpg'),
            'key_code' => 'KEY-WRONG',
            'confirm_check_in' => 1,
        ]);
        $stay = Stay::where('reservation_id', $reservation->id)->firstOrFail();

        $this->actingAs($receptionist)->from(route('receptionist.checkout.create', $stay))->post(route('receptionist.checkout.store', $stay), [
            'payment_amount' => 100000,
            'payment_method_id' => $method->id,
            'key_returned' => 1,
            'confirm_check_out' => 1,
        ])->assertRedirect(route('receptionist.checkout.create', $stay))->assertSessionHasErrors('payment_amount');

        $this->assertSame('active', $stay->fresh()->status->value);
        $this->assertSame('occupied', $room->fresh()->status->value);
        $this->assertSame('open', $stay->folio->fresh()->status);
    }

    private function room(): array
    {
        $roomType = RoomType::create([
            'code' => 'OPS-'.Str::upper(Str::random(7)),
            'name' => 'Operational Room '.Str::random(6),
            'slug' => 'operational-room-'.Str::lower(Str::random(10)),
            'capacity' => 3,
            'max_adults' => 2,
            'max_children' => 1,
            'bed_count' => 1,
            'base_price' => 700000,
            'extra_bed_price' => 0,
            'is_active' => true,
        ]);
        $room = Room::create([
            'room_type_id' => $roomType->id,
            'room_number' => 'OPS-'.Str::upper(Str::random(6)),
            'status' => 'available',
            'is_active' => true,
        ]);

        return [$roomType, $room];
    }

    private function confirmedReservation(Room $room): Reservation
    {
        $reservation = Reservation::create([
            'booking_code' => 'OPS-BOOK-'.Str::upper(Str::random(8)),
            'room_type_id' => $room->room_type_id,
            'room_id' => $room->id,
            'source' => 'walk_in',
            'guest_name' => 'Operational Guest',
            'guest_phone' => '6281299998888',
            'check_in_date' => today(),
            'check_out_date' => today()->addDay(),
            'total_nights' => 1,
            'adults' => 1,
            'children' => 0,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'currency' => 'IDR',
            'subtotal' => 700000,
            'grand_total' => 700000,
            'confirmed_at' => now(),
        ]);
        ReservationNight::create([
            'reservation_id' => $reservation->id,
            'stay_date' => today(),
            'rate_name' => 'Harga Dasar',
            'price_before_discount' => 700000,
            'discount_amount' => 0,
            'net_price' => 700000,
        ]);

        return $reservation;
    }

    private function manualMethod(): PaymentMethod
    {
        return PaymentMethod::query()->updateOrCreate(['code' => 'ops-cash-test'], [
            'name' => 'Operational Cash Test',
            'type' => 'cash',
            'channel' => 'manual',
            'is_online' => false,
            'is_active' => true,
        ]);
    }
}
