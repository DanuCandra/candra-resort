<?php

namespace Tests\Feature;

use App\Models\Folio;
use App\Models\GuestRoomAccess;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class OperationalFinanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_room_service_guest_can_render_running_bill(): void
    {
        [, $folio, , $token] = $this->financeContext();

        $this->withSession(['room_service_access_token' => $token])
            ->get(route('room-service.bill.show'))
            ->assertOk()
            ->assertSee('Tagihan Berjalan')
            ->assertSee($folio->folio_number)
            ->assertSee('Rp500.000');
    }

    public function test_receptionist_can_render_folio_and_payment_pages(): void
    {
        [, $folio, $method] = $this->financeContext();
        $receptionist = User::factory()->receptionist()->create();
        $payment = Payment::create([
            'payment_code' => 'PAY-'.Str::upper((string) Str::ulid()),
            'reservation_id' => $folio->reservation_id,
            'stay_id' => $folio->stay_id,
            'folio_id' => $folio->id,
            'payment_method_id' => $method->id,
            'received_by' => $receptionist->id,
            'purpose' => 'folio', 'status' => 'paid', 'source' => 'receptionist',
            'currency' => 'IDR', 'amount' => 100000, 'paid_at' => now(),
        ]);

        $this->actingAs($receptionist)->get(route('receptionist.folios.index'))->assertOk()->assertSee($folio->folio_number);
        $this->actingAs($receptionist)->get(route('receptionist.folios.show', $folio))->assertOk()->assertSee('Catat Pembayaran');
        $this->actingAs($receptionist)->get(route('receptionist.payments.index'))->assertOk()->assertSee($payment->payment_code);
        $this->actingAs($receptionist)->get(route('receptionist.payments.show', $payment))->assertOk()->assertSee('Rp100.000');
    }

    public function test_receptionist_can_record_partial_folio_payment_but_not_overpayment(): void
    {
        [, $folio, $method] = $this->financeContext();
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->post(route('receptionist.folios.payments.store', $folio), [
            'payment_amount' => 200000,
            'payment_method_id' => $method->id,
            'reference_number' => 'EDC-TEST-001',
        ])->assertRedirect(route('receptionist.folios.show', $folio));

        $folio->refresh();
        $this->assertSame(200000.0, (float) $folio->paid_amount);
        $this->assertSame(300000.0, (float) $folio->balance_amount);
        $this->assertDatabaseHas('payments', ['folio_id' => $folio->id, 'amount' => 200000, 'status' => 'paid']);
        $this->assertDatabaseHas('audit_logs', ['module' => 'payments', 'event' => 'payment_received']);

        $this->actingAs($receptionist)->from(route('receptionist.folios.show', $folio))
            ->post(route('receptionist.folios.payments.store', $folio), [
                'payment_amount' => 300001,
                'payment_method_id' => $method->id,
            ])->assertRedirect(route('receptionist.folios.show', $folio))->assertSessionHasErrors('payment_amount');
        $this->assertSame(1, $folio->payments()->count());
    }

    private function financeContext(): array
    {
        $roomType = RoomType::create([
            'code' => 'FIN-'.Str::upper(Str::random(6)), 'name' => 'Finance Room '.Str::random(5),
            'slug' => 'finance-room-'.Str::lower(Str::random(10)), 'capacity' => 2, 'max_adults' => 2,
            'max_children' => 0, 'bed_count' => 1, 'base_price' => 500000, 'extra_bed_price' => 0, 'is_active' => true,
        ]);
        $room = Room::create(['room_type_id' => $roomType->id, 'room_number' => 'FIN-'.Str::upper(Str::random(5)), 'status' => 'occupied', 'is_active' => true]);
        $reservation = Reservation::create([
            'booking_code' => 'FIN-BOOK-'.Str::upper(Str::random(7)), 'room_type_id' => $roomType->id, 'room_id' => $room->id,
            'source' => 'walk_in', 'guest_name' => 'Finance Guest', 'guest_phone' => '628123456789',
            'check_in_date' => today(), 'check_out_date' => today()->addDay(), 'total_nights' => 1,
            'adults' => 1, 'status' => 'checked_in', 'payment_status' => 'unpaid', 'currency' => 'IDR',
            'subtotal' => 500000, 'grand_total' => 500000,
        ]);
        $stay = Stay::create([
            'reservation_id' => $reservation->id, 'room_id' => $room->id, 'guest_name' => 'Finance Guest',
            'guest_phone' => '628123456789', 'check_in_at' => now(), 'status' => 'active',
        ]);
        $folio = Folio::create([
            'folio_number' => 'FOL-'.Str::upper((string) Str::ulid()), 'stay_id' => $stay->id,
            'reservation_id' => $reservation->id, 'status' => 'open', 'currency' => 'IDR',
            'subtotal' => 500000, 'total_amount' => 500000, 'balance_amount' => 500000,
        ]);
        $folio->items()->create(['item_type' => 'room', 'description' => 'Biaya kamar', 'quantity' => 1, 'unit_price' => 500000, 'amount' => 500000, 'posted_at' => now()]);
        $method = PaymentMethod::create([
            'name' => 'Cash Finance Test', 'code' => 'cash-fin-'.Str::lower(Str::random(6)),
            'type' => 'cash', 'channel' => 'manual', 'is_online' => false, 'is_active' => true,
        ]);
        $token = Str::random(80);
        $access = GuestRoomAccess::create([
            'stay_id' => $stay->id, 'room_id' => $room->id, 'session_token' => hash('sha256', $token),
            'phone_verified_at' => now(), 'expires_at' => today()->addDay()->endOfDay(),
        ]);

        return [$access, $folio, $method, $token];
    }
}
