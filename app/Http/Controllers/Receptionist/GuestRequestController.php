<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\GuestRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\GuestRequestStatusRequest;
use App\Models\GuestRequest;
use App\Services\GuestRequestService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = GuestRequest::query()->with(['room', 'stay', 'handledBy'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE status WHEN 'requested' THEN 1 WHEN 'accepted' THEN 2 WHEN 'processing' THEN 3 ELSE 4 END")
            ->latest('requested_at')->paginate(15)->withQueryString();

        return view('receptionist.guest-requests.index', ['requests' => $requests, 'statuses' => GuestRequestStatus::cases()]);
    }

    public function show(GuestRequest $guestRequest): View
    {
        return view('receptionist.guest-requests.show', ['guestRequest' => $guestRequest->load(['room', 'stay', 'handledBy'])]);
    }

    public function updateStatus(GuestRequestStatusRequest $request, GuestRequest $guestRequest, GuestRequestService $service): RedirectResponse
    {
        $old = $guestRequest->status->value;
        $status = GuestRequestStatus::from($request->validated('status'));
        $guestRequest = $service->transition($guestRequest, $status, $request->user());
        AuditLogger::record($request, 'status_change', 'guest_requests', $guestRequest, 'Mengubah status permintaan '.$guestRequest->request_code.' dari '.$old.' menjadi '.$status->value.'.');

        return back()->with('success', 'Status permintaan diperbarui menjadi '.$status->label().'.');
    }
}
