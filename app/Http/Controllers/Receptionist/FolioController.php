<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\FolioPaymentRequest;
use App\Models\Folio;
use App\Models\PaymentMethod;
use App\Services\ManualPaymentService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FolioController extends Controller
{
    public function index(Request $request): View
    {
        $folios = Folio::query()->with(['stay.room', 'reservation.roomType'])->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('folio_number', 'like', '%'.$request->string('search').'%')
                ->orWhereHas('stay', fn ($stay) => $stay->where('guest_name', 'like', '%'.$request->string('search').'%'))
                ->orWhereHas('reservation', fn ($reservation) => $reservation->where('booking_code', 'like', '%'.$request->string('search').'%'))))
            ->latest()->paginate(20)->withQueryString();

        return view('receptionist.folios.index', compact('folios'));
    }

    public function show(Folio $folio): View
    {
        $folio->load(['stay.room', 'reservation.roomType', 'items.postedBy', 'payments.method', 'payments.receivedBy']);

        return view('receptionist.folios.show', [
            'folio' => $folio,
            'paymentMethods' => PaymentMethod::query()->where('channel', 'manual')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function recordPayment(FolioPaymentRequest $request, Folio $folio, ManualPaymentService $payments): RedirectResponse
    {
        $payment = DB::transaction(function () use ($request, $folio, $payments) {
            $folio = Folio::query()->with(['reservation', 'stay'])->whereKey($folio->id)->lockForUpdate()->firstOrFail();
            if ($folio->status !== 'open') {
                throw ValidationException::withMessages(['payment_amount' => 'Folio sudah ditutup.']);
            }
            $outstanding = (int) round((float) $folio->total_amount) - (int) round((float) $folio->payments()->where('status', 'paid')->sum('amount'));
            $amount = (int) $request->validated('payment_amount');
            if ($amount > $outstanding) {
                throw ValidationException::withMessages(['payment_amount' => 'Nominal melebihi saldo folio Rp'.number_format($outstanding, 0, ',', '.').'.']);
            }
            $method = PaymentMethod::query()->whereKey($request->validated('payment_method_id'))->lockForUpdate()->firstOrFail();

            return $payments->record(
                $folio->reservation, $method, $amount, $request->user(), $folio->stay, $folio,
                'folio', $request->validated('reference_number'), $request->validated('notes') ?: 'Pembayaran folio oleh Receptionist.'
            );
        }, 3);
        AuditLogger::record($request, 'payment_received', 'payments', $payment, 'Mencatat pembayaran '.$payment->payment_code.' untuk folio '.$folio->folio_number.'.');

        return redirect()->route('receptionist.folios.show', $folio)->with('success', 'Pembayaran folio berhasil dicatat.');
    }
}
