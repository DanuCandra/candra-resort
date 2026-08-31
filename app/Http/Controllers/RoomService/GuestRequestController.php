<?php

namespace App\Http\Controllers\RoomService;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomService\GuestRequestRequest;
use App\Models\GuestRequest;
use App\Services\GuestRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Menangani permintaan bantuan dari portal kamar.
class GuestRequestController extends Controller
{
    public function index(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $requests = GuestRequest::query()->where('stay_id', $access->stay_id)->latest('requested_at')->paginate(10);

        return view('room-service.requests.index', compact('requests', 'access'));
    }

    public function store(GuestRequestRequest $request, GuestRequestService $service): RedirectResponse
    {
        $guestRequest = $service->place($request->attributes->get('roomServiceAccess'), $request->validated());

        return redirect()->route('room-service.requests.show', $guestRequest)->with('success', 'Permintaan '.$guestRequest->request_code.' berhasil dikirim.');
    }

    public function show(Request $request, GuestRequest $guestRequest): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        abort_unless($guestRequest->stay_id === $access->stay_id, 404);

        return view('room-service.requests.show', ['guestRequest' => $guestRequest->load(['room', 'handledBy']), 'access' => $access]);
    }
}
