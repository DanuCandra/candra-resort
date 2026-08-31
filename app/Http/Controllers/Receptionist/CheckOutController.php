<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\StayStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\CheckOutRequest;
use App\Models\PaymentMethod;
use App\Models\Stay;
use App\Services\CheckOutService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Menangani antrean dan proses check-out tamu.
class CheckOutController extends Controller
{
    // Menampilkan daftar tamu yang masih menginap.
    public function index(): View
    {
        $stays = Stay::query()->with(['reservation', 'room', 'folio'])->whereHas('folio')->where('status', StayStatus::Active->value)
            ->orderByRaw('CASE WHEN DATE(check_out_at) IS NULL THEN 0 ELSE 1 END')->latest('check_in_at')->paginate(15);

        return view('receptionist.checkout.index', compact('stays'));
    }

    // Menampilkan rincian dan tagihan check-out.
    public function create(Stay $stay): View
    {
        // Check-out hanya untuk stay aktif.
        abort_unless($stay->status === StayStatus::Active, 422);
        $stay->load(['reservation', 'room.roomType', 'folio.items', 'folio.payments.method']);

        // Menghitung total, pembayaran, dan sisa tagihan.
        $total = (int) round((float) $stay->folio->items->where('is_void', false)->sum('amount'));
        $paid = (int) round((float) $stay->folio->payments->filter(fn ($payment): bool => $payment->status->value === 'paid')->sum('amount'));

        return view('receptionist.checkout.create', [
            'stay' => $stay,
            'outstanding' => max(0, $total - $paid),
            'paymentMethods' => PaymentMethod::query()->where('channel', 'manual')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    // Memproses dan menyelesaikan check-out.
    public function store(CheckOutRequest $request, Stay $stay, CheckOutService $service): RedirectResponse
    {
        $completedStay = $service->checkOut($stay, $request->user(), $request->validated());

        // Mencatat aktivitas Receptionist.
        AuditLogger::record($request, 'check_out', 'stays', $completedStay, 'Check-out '.$completedStay->reservation->booking_code.' dari kamar '.$completedStay->room->room_number.'.');

        return redirect()->route('receptionist.reservations.show', $completedStay->reservation)->with('success', 'Check-out berhasil. QR dicabut dan kamar berstatus cleaning.');
    }
}
