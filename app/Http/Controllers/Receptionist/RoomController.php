<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\RoomRequest;
use App\Models\GuestRoomAccess;
use App\Models\Room;
use App\Models\RoomStatusHistory;
use App\Models\RoomType;
use App\Services\RoomQrService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::query()->with('roomType')
            ->when($request->filled('search'), fn ($query) => $query->where('room_number', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('room_type_id'), fn ($query) => $query->where('room_type_id', $request->integer('room_type_id')))
            ->orderBy('room_number')->paginate(15)->withQueryString();

        return view('receptionist.rooms.index', [
            'rooms' => $rooms,
            'roomTypes' => $this->roomTypes(),
            'statuses' => RoomStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('receptionist.rooms.create', ['roomTypes' => $this->roomTypes(), 'statuses' => RoomStatus::cases()]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $room = DB::transaction(function () use ($request): Room {
            $data = $request->safe()->except('status_reason');
            $room = Room::create($data);
            $this->recordStatus($room, null, $room->status->value, $request);

            return $room;
        });

        AuditLogger::record($request, 'create', 'rooms', $room, 'Membuat kamar nomor '.$room->room_number.'.', null, $room->toArray());

        return redirect()->route('receptionist.rooms.show', $room)->with('success', 'Kamar dan token QR berhasil dibuat.');
    }

    public function show(Room $room): View
    {
        $room->load(['roomType', 'statusHistories.changedBy'])->loadCount(['reservations', 'stays']);

        return view('receptionist.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        return view('receptionist.rooms.edit', ['room' => $room, 'roomTypes' => $this->roomTypes(), 'statuses' => RoomStatus::cases()]);
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $oldValues = $room->toArray();
        $oldStatus = $room->status->value;

        DB::transaction(function () use ($request, $room, $oldStatus): void {
            $room->update($request->safe()->except('status_reason'));
            if ($oldStatus !== $room->status->value) {
                $this->recordStatus($room, $oldStatus, $room->status->value, $request);
            }
        });

        AuditLogger::record($request, 'update', 'rooms', $room, 'Memperbarui kamar nomor '.$room->room_number.'.', $oldValues, $room->fresh()->toArray());

        return redirect()->route('receptionist.rooms.show', $room)->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Request $request, Room $room): RedirectResponse
    {
        if ($room->reservations()->exists() || $room->stays()->exists()) {
            $oldStatus = $room->status->value;
            DB::transaction(function () use ($request, $room, $oldStatus): void {
                $room->update(['is_active' => false, 'status' => RoomStatus::Unavailable]);
                if ($oldStatus !== RoomStatus::Unavailable->value) {
                    $this->recordStatus($room, $oldStatus, RoomStatus::Unavailable->value, $request, 'Kamar dinonaktifkan karena memiliki riwayat transaksi.');
                }
            });

            return back()->with('success', 'Kamar memiliki riwayat sehingga dinonaktifkan, bukan dihapus.');
        }

        $room->delete();
        AuditLogger::record($request, 'delete', 'rooms', $room, 'Menghapus kamar nomor '.$room->room_number.'.');

        return redirect()->route('receptionist.rooms.index')->with('success', 'Kamar berhasil dihapus.');
    }

    public function qr(Room $room, RoomQrService $qrService): View
    {
        return view('receptionist.rooms.qr', ['room' => $room->load('roomType'), 'accessUrl' => $qrService->accessUrl($room)]);
    }

    public function qrImage(Request $request, Room $room, RoomQrService $qrService): Response
    {
        $response = response($qrService->png($room))->header('Content-Type', 'image/png');
        if ($request->boolean('download')) {
            $response->header('Content-Disposition', 'attachment; filename="candra-resort-kamar-'.$room->room_number.'.png"');
        }

        return $response;
    }

    public function qrPrint(Room $room, RoomQrService $qrService): View
    {
        return view('receptionist.rooms.qr-print', ['room' => $room->load('roomType'), 'accessUrl' => $qrService->accessUrl($room)]);
    }

    public function regenerateQr(Request $request, Room $room): RedirectResponse
    {
        DB::transaction(function () use ($room): void {
            GuestRoomAccess::query()->where('room_id', $room->id)->whereNull('revoked_at')->update(['revoked_at' => now()]);
            $room->forceFill(['qr_token' => (string) Str::uuid()])->save();
        });
        AuditLogger::record($request, 'regenerate_qr', 'rooms', $room, 'Membuat ulang token QR kamar '.$room->room_number.'.');

        return back()->with('success', 'Token QR baru berhasil dibuat. QR lama tidak lagi berlaku.');
    }

    private function roomTypes()
    {
        return RoomType::query()->where('is_active', true)->orderBy('name')->get();
    }

    private function recordStatus(Room $room, ?string $oldStatus, string $newStatus, Request $request, ?string $reason = null): void
    {
        RoomStatusHistory::create([
            'room_id' => $room->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $request->user()?->id,
            'reason' => $reason ?: $request->input('status_reason'),
            'changed_at' => now(),
        ]);
    }
}
