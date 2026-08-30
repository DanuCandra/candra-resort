<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\RoomRateRequest;
use App\Models\RoomRate;
use App\Models\RoomType;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomRateController extends Controller
{
    public function index(Request $request): View
    {
        $rates = RoomRate::query()->with('roomType')
            ->when($request->filled('room_type_id'), fn ($query) => $query->where('room_type_id', $request->integer('room_type_id')))
            ->orderByDesc('priority')->latest()->paginate(12)->withQueryString();

        return view('receptionist.pricing.index', ['rates' => $rates, 'roomTypes' => $this->roomTypes()]);
    }

    public function create(): View
    {
        return view('receptionist.pricing.create', ['roomTypes' => $this->roomTypes()]);
    }

    public function store(RoomRateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['priority'] ??= 0;
        $data['created_by'] = $request->user()->id;
        $rate = RoomRate::create($data);
        AuditLogger::record($request, 'create', 'room_rates', $rate, 'Membuat harga '.$rate->name.'.', null, $rate->toArray());

        return redirect()->route('receptionist.pricing.index')->with('success', 'Aturan harga berhasil ditambahkan.');
    }

    public function edit(RoomRate $pricing): View
    {
        return view('receptionist.pricing.edit', ['rate' => $pricing, 'roomTypes' => $this->roomTypes()]);
    }

    public function update(RoomRateRequest $request, RoomRate $pricing): RedirectResponse
    {
        $oldValues = $pricing->toArray();
        $data = $request->validated();
        $data['priority'] ??= 0;
        $pricing->update($data);
        AuditLogger::record($request, 'update', 'room_rates', $pricing, 'Memperbarui harga '.$pricing->name.'.', $oldValues, $pricing->fresh()->toArray());

        return redirect()->route('receptionist.pricing.index')->with('success', 'Aturan harga berhasil diperbarui.');
    }

    public function destroy(Request $request, RoomRate $pricing): RedirectResponse
    {
        if ($pricing->reservationNights()->exists()) {
            $pricing->update(['is_active' => false]);

            return back()->with('success', 'Harga memiliki riwayat reservasi sehingga dinonaktifkan.');
        }

        $pricing->delete();
        AuditLogger::record($request, 'delete', 'room_rates', $pricing, 'Menghapus aturan harga '.$pricing->name.'.');

        return back()->with('success', 'Aturan harga berhasil dihapus.');
    }

    private function roomTypes()
    {
        return RoomType::query()->where('is_active', true)->orderBy('name')->get();
    }
}
