<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OwnerReportsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_owner_can_open_every_report_and_receptionist_cannot(): void
    {
        $owner = User::factory()->owner()->create();
        $receptionist = User::factory()->receptionist()->create();
        $routes = [
            'owner.reports.reservations' => 'Laporan Reservasi',
            'owner.reports.occupancy' => 'Laporan Okupansi',
            'owner.reports.revenue' => 'Laporan Pendapatan',
            'owner.reports.payments' => 'Laporan Pembayaran',
            'owner.reports.services' => 'Laporan Layanan',
            'owner.reports.monthly' => 'Laporan Bulanan',
        ];

        foreach ($routes as $route => $heading) {
            $this->actingAs($owner)->get(route($route))->assertOk()->assertSee($heading);
            $this->actingAs($receptionist)->get(route($route))->assertForbidden();
        }
    }

    public function test_owner_dashboard_and_reports_use_paid_transaction_and_actual_stay_data(): void
    {
        $owner = User::factory()->owner()->create();
        [$reservation, $room] = $this->createReservation();
        $method = PaymentMethod::query()->create([
            'name' => 'Midtrans', 'code' => 'midtrans-report-test', 'type' => 'gateway',
            'channel' => 'midtrans', 'is_online' => true, 'is_active' => true,
        ]);
        Payment::query()->create([
            'payment_code' => 'PAY-REPORT-001', 'reservation_id' => $reservation->id,
            'payment_method_id' => $method->id, 'purpose' => 'reservation', 'status' => 'paid',
            'source' => 'midtrans', 'currency' => 'IDR', 'amount' => 1250000, 'paid_at' => now(),
        ]);
        Stay::query()->create([
            'reservation_id' => $reservation->id, 'room_id' => $room->id,
            'guest_name' => 'Guest Report', 'guest_phone' => '628123456789',
            'check_in_at' => now()->subHours(2), 'status' => 'active',
        ]);

        $this->actingAs($owner)->get(route('owner.dashboard'))
            ->assertOk()->assertSee('Rp1.250.000')->assertSee('Room-night Terisi');
        $this->actingAs($owner)->get(route('owner.reports.revenue'))
            ->assertOk()->assertSee('PAY-REPORT-001')->assertSee('Rp1.250.000');
        $this->actingAs($owner)->get(route('owner.reports.occupancy'))
            ->assertOk()->assertSee('Okupansi Harian');
    }

    public function test_custom_period_is_validated_and_csv_can_be_exported(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get(route('owner.reports.reservations', [
            'period' => 'custom', 'start_date' => '2026-08-20', 'end_date' => '2026-08-01',
        ]))->assertSessionHasErrors('end_date');

        $response = $this->actingAs($owner)->get(route('owner.reports.export', [
            'report' => 'monthly', 'period' => 'this_year',
        ]));

        $response->assertOk()->assertDownload();
        $this->assertStringContainsString('Bulan;Reservasi;Pembatalan;Pendapatan', $response->streamedContent());
    }

    /** @return array{Reservation, Room} */
    private function createReservation(): array
    {
        $roomType = RoomType::query()->create([
            'code' => 'RPT-DLX', 'name' => 'Deluxe Report', 'slug' => 'deluxe-report',
            'capacity' => 2, 'max_adults' => 2, 'max_children' => 1, 'bed_count' => 1,
            'base_price' => 1250000, 'extra_bed_price' => 0, 'is_active' => true,
        ]);
        $room = Room::query()->create([
            'room_type_id' => $roomType->id, 'room_number' => 'RPT-101',
            'status' => 'occupied', 'is_active' => true,
        ]);
        $reservation = Reservation::query()->create([
            'booking_code' => 'RSV-REPORT-001', 'room_type_id' => $roomType->id,
            'room_id' => $room->id, 'source' => 'online', 'guest_name' => 'Guest Report',
            'guest_phone' => '628123456789', 'check_in_date' => today(),
            'check_out_date' => today()->addDay(), 'total_nights' => 1, 'adults' => 2,
            'status' => 'checked_in', 'payment_status' => 'paid', 'currency' => 'IDR',
            'subtotal' => 1250000, 'grand_total' => 1250000,
        ]);

        return [$reservation, $room];
    }
}
