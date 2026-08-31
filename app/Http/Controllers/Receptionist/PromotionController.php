<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\PromotionRequest;
use App\Models\Promotion;
use App\Models\RoomType;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

// Mengelola kode dan aturan promosi.
class PromotionController extends Controller
{
    public function index(Request $request): View
    {
        $promotions = Promotion::query()->with('roomTypes')
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('code', 'like', '%'.$request->string('search').'%')))
            ->latest()->paginate(12)->withQueryString();

        return view('receptionist.promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        return view('receptionist.promotions.create', ['roomTypes' => $this->roomTypes()]);
    }

    public function store(PromotionRequest $request): RedirectResponse
    {
        $promotion = DB::transaction(function () use ($request): Promotion {
            $data = $request->safe()->except('room_types');
            $data['created_by'] = $request->user()->id;
            $promotion = Promotion::create($data);
            $promotion->roomTypes()->sync($request->validated('room_types', []));

            return $promotion;
        });
        AuditLogger::record($request, 'create', 'promotions', $promotion, 'Membuat promosi '.$promotion->code.'.', null, $promotion->toArray());

        return redirect()->route('receptionist.promotions.index')->with('success', 'Promosi berhasil ditambahkan.');
    }

    public function edit(Promotion $promotion): View
    {
        $promotion->load('roomTypes');

        return view('receptionist.promotions.edit', ['promotion' => $promotion, 'roomTypes' => $this->roomTypes()]);
    }

    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $oldValues = $promotion->toArray();
        DB::transaction(function () use ($request, $promotion): void {
            $promotion->update($request->safe()->except('room_types'));
            $promotion->roomTypes()->sync($request->validated('room_types', []));
        });
        AuditLogger::record($request, 'update', 'promotions', $promotion, 'Memperbarui promosi '.$promotion->code.'.', $oldValues, $promotion->fresh()->toArray());

        return redirect()->route('receptionist.promotions.index')->with('success', 'Promosi berhasil diperbarui.');
    }

    public function destroy(Request $request, Promotion $promotion): RedirectResponse
    {
        if ($promotion->reservations()->exists()) {
            $promotion->update(['is_active' => false]);

            return back()->with('success', 'Promosi memiliki riwayat reservasi sehingga dinonaktifkan.');
        }

        $promotion->delete();
        AuditLogger::record($request, 'delete', 'promotions', $promotion, 'Menghapus promosi '.$promotion->code.'.');

        return back()->with('success', 'Promosi berhasil dihapus.');
    }

    private function roomTypes()
    {
        return RoomType::query()->where('is_active', true)->orderBy('name')->get();
    }
}
