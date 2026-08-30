<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceptionistGuestDirectoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_directory_combines_registered_guests_and_checked_in_walk_in_guests(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $neverStayed = User::factory()->create(['name' => 'Guest Baru Register', 'phone' => '628111000001']);
        $returning = User::factory()->create(['name' => 'Guest Pernah Menginap', 'phone' => '628111000002']);
        [$roomType, $rooms] = $this->createRooms();

        $registeredReservation = $this->createReservation('RSV-GUEST-DIR-01', $roomType, $rooms[0], [
            'guest_id' => $returning->id, 'guest_name' => $returning->name,
            'guest_email' => $returning->email, 'guest_phone' => $returning->phone,
        ]);
        Stay::query()->create([
            'reservation_id' => $registeredReservation->id, 'guest_id' => $returning->id,
            'room_id' => $rooms[0]->id, 'guest_name' => $returning->name,
            'guest_phone' => $returning->phone, 'check_in_at' => now()->subDays(3),
            'check_out_at' => now()->subDays(2), 'status' => 'completed',
        ]);
        $mergedWalkInReservation = $this->createReservation('RSV-GUEST-DIR-03', $roomType, $rooms[2], [
            'source' => 'walk_in', 'guest_name' => 'Nama Snapshot Walk-in',
            'guest_email' => null, 'guest_phone' => $returning->phone,
        ]);
        Stay::query()->create([
            'reservation_id' => $mergedWalkInReservation->id, 'room_id' => $rooms[2]->id,
            'guest_name' => 'Nama Snapshot Walk-in', 'guest_phone' => $returning->phone,
            'check_in_at' => now()->subDays(10), 'check_out_at' => now()->subDays(9), 'status' => 'completed',
        ]);

        $walkInReservation = $this->createReservation('RSV-GUEST-DIR-02', $roomType, $rooms[1], [
            'source' => 'walk_in', 'guest_name' => 'Tamu Datang Langsung',
            'guest_email' => null, 'guest_phone' => '628111000003',
        ]);
        $walkInStay = Stay::query()->create([
            'reservation_id' => $walkInReservation->id, 'room_id' => $rooms[1]->id,
            'guest_name' => 'Tamu Datang Langsung', 'guest_phone' => '628111000003',
            'check_in_at' => now()->subHour(), 'status' => 'active',
        ]);

        $this->actingAs($receptionist)->get(route('receptionist.guests.index'))
            ->assertOk()
            ->assertSee($neverStayed->name)
            ->assertSee($returning->name)
            ->assertSee('Tamu Datang Langsung')
            ->assertDontSee('Nama Snapshot Walk-in')
            ->assertSee('Email tidak tersedia');

        $this->actingAs($receptionist)->get(route('receptionist.guests.index', ['status' => 'registered_only']))
            ->assertOk()->assertSee($neverStayed->name)->assertDontSee($returning->name)->assertDontSee('Tamu Datang Langsung');

        $this->actingAs($receptionist)->get(route('receptionist.guests.walk-ins.show', $walkInStay))
            ->assertOk()->assertSee('Tamu Walk-in')->assertSee('Tidak tersedia')->assertSee('RSV-GUEST-DIR-02');

        $this->actingAs($receptionist)->get(route('receptionist.guests.accounts.show', $returning))
            ->assertOk()->assertSee('RSV-GUEST-DIR-01')->assertSee('RSV-GUEST-DIR-03');
    }

    public function test_registered_guest_without_check_in_has_profile_and_empty_history(): void
    {
        $receptionist = User::factory()->receptionist()->create();
        $guest = User::factory()->create(['name' => 'Akun Tanpa Kunjungan', 'phone' => '628122220000']);

        $this->actingAs($receptionist)->get(route('receptionist.guests.accounts.show', $guest))
            ->assertOk()->assertSee('Akun Tanpa Kunjungan')->assertSee('belum pernah check-in');
    }

    public function test_guest_directory_is_receptionist_only(): void
    {
        $guest = User::factory()->create();
        $owner = User::factory()->owner()->create();

        $this->actingAs($guest)->get(route('receptionist.guests.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('receptionist.guests.index'))->assertForbidden();
    }

    /** @return array{RoomType, array<int, Room>} */
    private function createRooms(): array
    {
        $roomType = RoomType::query()->create([
            'code' => 'GUEST-DIR', 'name' => 'Guest Directory Room', 'slug' => 'guest-directory-room',
            'capacity' => 2, 'max_adults' => 2, 'max_children' => 0, 'bed_count' => 1,
            'base_price' => 500000, 'extra_bed_price' => 0, 'is_active' => true,
        ]);

        return [$roomType, [
            Room::query()->create(['room_type_id' => $roomType->id, 'room_number' => 'GD-101', 'status' => 'available', 'is_active' => true]),
            Room::query()->create(['room_type_id' => $roomType->id, 'room_number' => 'GD-102', 'status' => 'occupied', 'is_active' => true]),
            Room::query()->create(['room_type_id' => $roomType->id, 'room_number' => 'GD-103', 'status' => 'available', 'is_active' => true]),
        ]];
    }

    /** @param array<string, mixed> $overrides */
    private function createReservation(string $code, RoomType $roomType, Room $room, array $overrides): Reservation
    {
        return Reservation::query()->create(array_merge([
            'booking_code' => $code, 'room_type_id' => $roomType->id, 'room_id' => $room->id,
            'source' => 'online', 'guest_name' => 'Guest', 'guest_phone' => '628100000000',
            'check_in_date' => today()->subDays(3), 'check_out_date' => today()->subDays(2),
            'total_nights' => 1, 'adults' => 1, 'status' => 'checked_out',
            'payment_status' => 'paid', 'currency' => 'IDR', 'subtotal' => 500000,
            'grand_total' => 500000,
        ], $overrides));
    }
}
