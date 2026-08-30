<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()->with(['method', 'reservation', 'stay.room', 'receivedBy'])
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('payment_code', 'like', '%'.$request->string('search').'%')
                ->orWhere('reference_number', 'like', '%'.$request->string('search').'%')
                ->orWhereHas('reservation', fn ($reservation) => $reservation->where('booking_code', 'like', '%'.$request->string('search').'%')->orWhere('guest_name', 'like', '%'.$request->string('search').'%'))))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('method'), fn ($query) => $query->where('payment_method_id', $request->integer('method')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest()->paginate(20)->withQueryString();

        return view('receptionist.payments.index', [
            'payments' => $payments,
            'methods' => PaymentMethod::query()->orderBy('sort_order')->orderBy('name')->get(),
            'statuses' => PaymentStatus::cases(),
            'summary' => Payment::query()->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(CASE WHEN status = ? THEN amount ELSE 0 END), 0) as paid_total, COALESCE(SUM(CASE WHEN status = ? THEN amount ELSE 0 END), 0) as pending_total', [PaymentStatus::Paid->value, PaymentStatus::Pending->value])->first(),
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load(['method', 'reservation.roomType', 'stay.room', 'folio', 'receivedBy']);

        return view('receptionist.payments.show', compact('payment'));
    }
}
