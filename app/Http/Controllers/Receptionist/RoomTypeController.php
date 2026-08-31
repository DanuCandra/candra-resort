<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\RoomTypeRequest;
use App\Models\Facility;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// Mengelola tipe kamar, fasilitas, dan galeri fotonya.
class RoomTypeController extends Controller
{
    public function index(Request $request): View
    {
        $roomTypes = RoomType::query()
            ->with('images')->withCount(['rooms', 'facilities'])
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('code', 'like', '%'.$request->string('search').'%')))
            ->orderBy('sort_order')->orderBy('name')->paginate(10)->withQueryString();

        return view('receptionist.room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        return view('receptionist.room-types.create', ['facilities' => $this->facilities()]);
    }

    public function store(RoomTypeRequest $request): RedirectResponse
    {
        $roomType = DB::transaction(function () use ($request): RoomType {
            $data = $request->safe()->except(['facilities', 'images']);
            $data['sort_order'] ??= 0;
            $roomType = RoomType::create($data);
            $roomType->facilities()->sync($request->validated('facilities', []));
            $this->storeImages($roomType, $request);

            return $roomType;
        });

        AuditLogger::record($request, 'create', 'room_types', $roomType, 'Membuat tipe kamar '.$roomType->name.'.', null, $roomType->toArray());

        return redirect()->route('receptionist.room-types.show', $roomType)->with('success', 'Tipe kamar berhasil ditambahkan.');
    }

    public function show(RoomType $roomType): View
    {
        $roomType->load(['images', 'facilities', 'rooms'])->loadCount(['rooms', 'reservations']);

        return view('receptionist.room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType): View
    {
        $roomType->load(['images', 'facilities']);

        return view('receptionist.room-types.edit', ['roomType' => $roomType, 'facilities' => $this->facilities()]);
    }

    public function update(RoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $oldValues = $roomType->toArray();

        DB::transaction(function () use ($request, $roomType): void {
            $data = $request->safe()->except(['facilities', 'images']);
            $data['sort_order'] ??= 0;
            $roomType->update($data);
            $roomType->facilities()->sync($request->validated('facilities', []));
            $this->storeImages($roomType, $request);
        });

        AuditLogger::record($request, 'update', 'room_types', $roomType, 'Memperbarui tipe kamar '.$roomType->name.'.', $oldValues, $roomType->fresh()->toArray());

        return redirect()->route('receptionist.room-types.show', $roomType)->with('success', 'Tipe kamar berhasil diperbarui.');
    }

    public function destroy(Request $request, RoomType $roomType): RedirectResponse
    {
        if ($roomType->rooms()->exists() || $roomType->reservations()->exists()) {
            $roomType->update(['is_active' => false]);
            AuditLogger::record($request, 'deactivate', 'room_types', $roomType, 'Menonaktifkan tipe kamar '.$roomType->name.' karena memiliki riwayat terkait.');

            return back()->with('success', 'Tipe kamar memiliki data terkait sehingga dinonaktifkan, bukan dihapus.');
        }

        $roomType->delete();
        AuditLogger::record($request, 'delete', 'room_types', $roomType, 'Menghapus tipe kamar '.$roomType->name.'.');

        return redirect()->route('receptionist.room-types.index')->with('success', 'Tipe kamar berhasil dihapus.');
    }

    public function setPrimaryImage(Request $request, RoomType $roomType, RoomTypeImage $image): RedirectResponse
    {
        abort_unless($image->room_type_id === $roomType->id, 404);

        DB::transaction(function () use ($roomType, $image): void {
            $roomType->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        AuditLogger::record($request, 'update', 'room_types', $roomType, 'Mengubah foto utama tipe kamar '.$roomType->name.'.');

        return back()->with('success', 'Foto utama berhasil diubah.');
    }

    public function destroyImage(Request $request, RoomType $roomType, RoomTypeImage $image): RedirectResponse
    {
        abort_unless($image->room_type_id === $roomType->id, 404);
        $wasPrimary = $image->is_primary;
        $path = $image->image_path;

        DB::transaction(function () use ($roomType, $image, $wasPrimary): void {
            $image->delete();
            if ($wasPrimary && $nextImage = $roomType->images()->first()) {
                $nextImage->update(['is_primary' => true]);
            }
        });

        Storage::disk('public')->delete($path);
        AuditLogger::record($request, 'delete_image', 'room_types', $roomType, 'Menghapus foto tipe kamar '.$roomType->name.'.');

        return back()->with('success', 'Foto kamar berhasil dihapus.');
    }

    private function facilities()
    {
        return Facility::query()->where('is_active', true)->whereIn('scope', ['room', 'both'])->orderBy('sort_order')->orderBy('name')->get();
    }

    private function storeImages(RoomType $roomType, RoomTypeRequest $request): void
    {
        $startingOrder = (int) $roomType->images()->max('sort_order');
        $hasPrimary = $roomType->images()->where('is_primary', true)->exists();

        foreach ($request->file('images', []) as $index => $image) {
            $roomType->images()->create([
                'image_path' => $image->store('room-types/'.$roomType->id, 'public'),
                'alt_text' => $roomType->name,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $startingOrder + $index + 1,
            ]);
        }
    }
}
