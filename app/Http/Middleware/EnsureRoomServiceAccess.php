<?php

namespace App\Http\Middleware;

use App\Models\GuestRoomAccess;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoomServiceAccess
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $plainToken = $request->session()->get('room_service_access_token');
        $access = $plainToken
            ? GuestRoomAccess::query()->with(['stay', 'room'])->where('session_token', hash('sha256', $plainToken))->first()
            : null;

        if (! $access || ! $access->isUsable()) {
            $request->session()->forget('room_service_access_token');

            return redirect()->route('home')->with('error', 'Akses layanan kamar tidak aktif. Silakan pindai QR di kamar dan verifikasi kembali.');
        }

        $access->update(['last_accessed_at' => now()]);
        $request->attributes->set('roomServiceAccess', $access);

        return $next($request);
    }
}
