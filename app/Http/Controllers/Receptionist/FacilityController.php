<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\FacilityRequest;
use App\Models\Facility;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Mengelola data fasilitas hotel dan kamar.
class FacilityController extends Controller
{
    public function index(Request $request): View
    {
        $facilities = Facility::query()
            ->withCount('roomTypes')
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('description', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('scope'), fn ($query) => $query->where('scope', $request->string('scope')))
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();

        return view('receptionist.facilities.index', compact('facilities'));
    }

    public function create(): View
    {
        return view('receptionist.facilities.create');
    }

    public function store(FacilityRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] ??= 0;
        $facility = Facility::create($data);

        AuditLogger::record($request, 'create', 'facilities', $facility, 'Membuat fasilitas '.$facility->name.'.', null, $facility->toArray());

        return redirect()->route('receptionist.facilities.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility): View
    {
        return view('receptionist.facilities.edit', compact('facility'));
    }

    public function update(FacilityRequest $request, Facility $facility): RedirectResponse
    {
        $oldValues = $facility->toArray();
        $data = $request->validated();
        $data['sort_order'] ??= 0;
        $facility->update($data);

        AuditLogger::record($request, 'update', 'facilities', $facility, 'Memperbarui fasilitas '.$facility->name.'.', $oldValues, $facility->fresh()->toArray());

        return redirect()->route('receptionist.facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Request $request, Facility $facility): RedirectResponse
    {
        $name = $facility->name;
        $facility->delete();

        AuditLogger::record($request, 'delete', 'facilities', $facility, 'Menonaktifkan fasilitas '.$name.'.');

        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}
