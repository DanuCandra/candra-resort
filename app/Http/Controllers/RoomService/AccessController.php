<?php

namespace App\Http\Controllers\RoomService;

use App\Enums\StayStatus;
use App\Http\Controllers\Controller;
use App\Models\GuestRoomAccess;
use App\Models\Room;
use App\Models\Stay;
use App\Support\PhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccessController extends Controller
{
    public function show(string $qrToken): View
    {
        $room = $this->room($qrToken);

        return view('room-service.verify', [
            'room' => $room,
            'hasActiveStay' => $this->activeStay($room) !== null,
        ]);
    }

    public function verify(Request $request, string $qrToken): RedirectResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        $room = $this->room($qrToken);
        $stay = $this->activeStay($room);

        if (! $stay || ! hash_equals((string) PhoneNumber::normalize($stay->guest_phone), (string) PhoneNumber::normalize($request->string('phone')->toString()))) {
            throw ValidationException::withMessages([
                'phone' => 'Nomor telepon tidak cocok dengan data tamu yang sedang menginap.',
            ]);
        }

        $plainToken = Str::random(80);
        GuestRoomAccess::create([
            'stay_id' => $stay->id,
            'room_id' => $room->id,
            'session_token' => hash('sha256', $plainToken),
            'phone_verified_at' => now(),
            'last_accessed_at' => now(),
            'expires_at' => $stay->reservation?->check_out_date?->endOfDay(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $request->session()->put('room_service_access_token', $plainToken);
        $request->session()->regenerate();

        return redirect()->route('room-service.home')->with('success', 'Verifikasi berhasil. Selamat datang di layanan kamar Candra Resort.');
    }

    public function home(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $access->load(['stay.folio', 'room.roomType']);

        return view('room-service.home', compact('access'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $access = $request->attributes->get('roomServiceAccess');
        $access->update(['revoked_at' => now()]);
        $request->session()->forget('room_service_access_token');

        return redirect()->route('home')->with('success', 'Sesi layanan kamar telah ditutup.');
    }

    private function room(string $qrToken): Room
    {
        return Room::query()->where('qr_token', $qrToken)->where('is_active', true)->firstOrFail();
    }

    private function activeStay(Room $room): ?Stay
    {
        return $room->stays()->with('reservation')->where('status', StayStatus::Active->value)->latest('check_in_at')->first();
    }
}
