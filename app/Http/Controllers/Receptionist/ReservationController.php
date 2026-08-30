<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\WalkInReservationRequest;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\WalkInReservationService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = Reservation::query()->with(['roomType', 'room', 'stay'])
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('booking_code', 'like', '%'.$request->string('search').'%')
                ->orWhere('guest_name', 'like', '%'.$request->string('search').'%')
                ->orWhere('guest_phone', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('arrival_date'), fn ($query) => $query->whereDate('check_in_date', $request->date('arrival_date')))
            ->latest()->paginate(15)->withQueryString();

        return view('receptionist.reservations.index', ['reservations' => $reservations, 'statuses' => ReservationStatus::cases()]);
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['roomType', 'room', 'nights', 'payments.method', 'stay.folio', 'promotion']);

        return view('receptionist.reservations.show', compact('reservation'));
    }

    public function createWalkIn(): View
    {
        return view('receptionist.reservations.walk-in', [
            'rooms' => Room::query()->with('roomType')->where('is_active', true)->whereNotIn('status', ['maintenance', 'unavailable'])->orderBy('room_number')->get(),
            'paymentMethods' => PaymentMethod::query()->where('channel', 'manual')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function storeWalkIn(WalkInReservationRequest $request, WalkInReservationService $service): RedirectResponse
    {
        $reservation = $service->create($request->user(), $request->validated());
        AuditLogger::record($request, 'create', 'reservations', $reservation, 'Receptionist membuat reservasi walk-in '.$reservation->booking_code.'.', null, $reservation->toArray());

        return redirect()->route('receptionist.reservations.show', $reservation)->with('success', 'Reservasi walk-in berhasil dibuat.');
    }
}
