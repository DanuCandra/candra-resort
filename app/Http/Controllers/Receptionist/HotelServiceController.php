<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\HotelServiceRequest;
use App\Models\HotelService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// Mengelola layanan hotel yang dapat dipesan tamu.
class HotelServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = HotelService::query()->withCount('orders')
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested->where('name', 'like', '%'.$request->string('search').'%')->orWhere('code', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();

        return view('receptionist.hotel-services.index', compact('services'));
    }

    public function create(): View
    {
        return view('receptionist.hotel-services.create');
    }

    public function store(HotelServiceRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('hotel-services', 'public');
        }
        $service = HotelService::create($data);
        AuditLogger::record($request, 'create', 'hotel_services', $service, 'Membuat layanan '.$service->name.'.', null, $service->toArray());

        return redirect()->route('receptionist.hotel-services.index')->with('success', 'Layanan hotel berhasil ditambahkan.');
    }

    public function edit(HotelService $hotelService): View
    {
        return view('receptionist.hotel-services.edit', compact('hotelService'));
    }

    public function update(HotelServiceRequest $request, HotelService $hotelService): RedirectResponse
    {
        $old = $hotelService->toArray();
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $oldImage = $hotelService->image_path;
            $data['image_path'] = $request->file('image')->store('hotel-services', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }
        $hotelService->update($data);
        AuditLogger::record($request, 'update', 'hotel_services', $hotelService, 'Memperbarui layanan '.$hotelService->name.'.', $old, $hotelService->fresh()->toArray());

        return redirect()->route('receptionist.hotel-services.index')->with('success', 'Layanan hotel berhasil diperbarui.');
    }

    public function destroy(Request $request, HotelService $hotelService): RedirectResponse
    {
        if ($hotelService->orders()->exists()) {
            $hotelService->update(['is_active' => false, 'is_available' => false]);
            AuditLogger::record($request, 'deactivate', 'hotel_services', $hotelService, 'Menonaktifkan layanan '.$hotelService->name.' karena memiliki histori.');

            return back()->with('success', 'Layanan memiliki histori sehingga dinonaktifkan, bukan dihapus.');
        }
        $image = $hotelService->image_path;
        $hotelService->delete();
        if ($image) {
            Storage::disk('public')->delete($image);
        }
        AuditLogger::record($request, 'delete', 'hotel_services', $hotelService, 'Menghapus layanan '.$hotelService->name.'.');

        return back()->with('success', 'Layanan hotel berhasil dihapus.');
    }
}
