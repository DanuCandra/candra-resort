<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\PaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        $paymentMethods = PaymentMethod::query()->withCount('payments')
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('code', 'like', '%'.$request->string('search').'%')))
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();

        return view('receptionist.payment-methods.index', compact('paymentMethods'));
    }

    public function create(): View
    {
        return view('receptionist.payment-methods.create');
    }

    public function store(PaymentMethodRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] ??= 0;
        $data['created_by'] = $request->user()->id;
        $paymentMethod = PaymentMethod::create($data);
        AuditLogger::record($request, 'create', 'payment_methods', $paymentMethod, 'Membuat metode pembayaran '.$paymentMethod->name.'.', null, $paymentMethod->toArray());

        return redirect()->route('receptionist.payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('receptionist.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $oldValues = $paymentMethod->toArray();
        $data = $request->validated();
        $data['sort_order'] ??= 0;
        $paymentMethod->update($data);
        AuditLogger::record($request, 'update', 'payment_methods', $paymentMethod, 'Memperbarui metode pembayaran '.$paymentMethod->name.'.', $oldValues, $paymentMethod->fresh()->toArray());

        return redirect()->route('receptionist.payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        if ($paymentMethod->payments()->exists()) {
            $paymentMethod->update(['is_active' => false]);
            AuditLogger::record($request, 'deactivate', 'payment_methods', $paymentMethod, 'Menonaktifkan metode pembayaran '.$paymentMethod->name.' karena memiliki riwayat transaksi.');

            return back()->with('success', 'Metode sudah digunakan sehingga dinonaktifkan, bukan dihapus.');
        }

        $paymentMethod->delete();
        AuditLogger::record($request, 'delete', 'payment_methods', $paymentMethod, 'Menghapus metode pembayaran '.$paymentMethod->name.'.');

        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
