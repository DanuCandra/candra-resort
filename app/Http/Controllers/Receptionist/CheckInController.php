<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\CheckInRequest;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Stay;
use App\Services\CheckInService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheckInController extends Controller
{
    public function index(): View
    {
        $reservations = Reservation::query()->with(['roomType', 'room'])->whereIn('status', [ReservationStatus::Paid->value, ReservationStatus::Confirmed->value])
            ->whereDate('check_in_date', '<=', today())->orderBy('check_in_date')->paginate(15);

        return view('receptionist.checkin.index', compact('reservations'));
    }

    public function create(Reservation $reservation): View
    {
        abort_unless(in_array($reservation->status, [ReservationStatus::Paid, ReservationStatus::Confirmed], true), 422);

        return view('receptionist.checkin.create', [
            'reservation' => $reservation->load(['roomType', 'payments']),
            'rooms' => Room::query()->where('room_type_id', $reservation->room_type_id)->where('is_active', true)->whereNotIn('status', ['maintenance', 'unavailable'])->orderBy('room_number')->get(),
            'paymentMethods' => PaymentMethod::query()->where('channel', 'manual')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(CheckInRequest $request, Reservation $reservation, CheckInService $service): RedirectResponse
    {
        $stay = $service->checkIn($reservation, $request->user(), $request->validated(), $request->file('identity_photo'));
        AuditLogger::record($request, 'check_in', 'stays', $stay, 'Check-in '.$reservation->booking_code.' ke kamar '.$stay->room->room_number.'.');

        return redirect()->route('receptionist.reservations.show', $reservation)->with('success', 'Check-in berhasil. Stay dan folio tamu telah aktif.');
    }

    public function identity(Request $request, Stay $stay): StreamedResponse
    {
        abort_unless($stay->identity_photo_path && Storage::disk('local')->exists($stay->identity_photo_path), 404);
        AuditLogger::record($request, 'view_identity', 'stays', $stay, 'Receptionist mengakses dokumen identitas stay '.$stay->id.'.');

        return Storage::disk('local')->download($stay->identity_photo_path, 'identitas-'.$stay->reservation->booking_code.'.'.pathinfo($stay->identity_photo_path, PATHINFO_EXTENSION));
    }
}
