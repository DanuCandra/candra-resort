<?php

namespace App\Http\Controllers\RoomService;

use App\Enums\ServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomService\ServiceOrderRequest;
use App\Models\HotelService;
use App\Models\ServiceOrder;
use App\Services\ServiceOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Menangani pemesanan dan pemantauan layanan hotel.
class ServiceOrderController extends Controller
{
    public function index(Request $request): View
    {
        $serviceItems = HotelService::query()->where('is_active', true)->where('is_available', true)
            ->orderBy('sort_order')->orderBy('name')->get();
        $services = $serviceItems->groupBy('category');
        $access = $request->attributes->get('roomServiceAccess');

        return view('room-service.services.index', compact('services', 'serviceItems', 'access'));
    }

    public function store(ServiceOrderRequest $request, ServiceOrderService $service): RedirectResponse
    {
        $order = $service->place($request->attributes->get('roomServiceAccess'), $request->validated());

        return redirect()->route('room-service.services.show', $order)->with('success', 'Permintaan layanan '.$order->order_code.' berhasil dikirim.');
    }

    public function orders(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $status = $request->string('status')->toString();
        $statuses = ServiceOrderStatus::cases();
        $selectedStatus = collect($statuses)->contains(
            fn (ServiceOrderStatus $item): bool => $item->value === $status
        ) ? $status : null;
        $baseQuery = ServiceOrder::query()->where('stay_id', $access->stay_id);
        $summary = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->whereIn('status', [
                ServiceOrderStatus::Requested->value,
                ServiceOrderStatus::Accepted->value,
                ServiceOrderStatus::Scheduled->value,
                ServiceOrderStatus::Processing->value,
            ])->count(),
            'completed' => (clone $baseQuery)->where('status', ServiceOrderStatus::Completed->value)->count(),
            'amount' => (float) (clone $baseQuery)
                ->where('status', '!=', ServiceOrderStatus::Cancelled->value)
                ->sum('total_amount'),
        ];
        $orders = (clone $baseQuery)
            ->with('service')
            ->when($selectedStatus, fn ($query) => $query->where('status', $selectedStatus))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('room-service.services.orders', compact(
            'orders',
            'access',
            'statuses',
            'selectedStatus',
            'summary'
        ));
    }

    public function show(Request $request, ServiceOrder $serviceOrder): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        abort_unless($serviceOrder->stay_id === $access->stay_id, 404);

        return view('room-service.services.show', ['order' => $serviceOrder->load(['service', 'room']), 'access' => $access]);
    }
}
