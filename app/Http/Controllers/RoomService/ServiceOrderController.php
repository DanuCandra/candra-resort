<?php

namespace App\Http\Controllers\RoomService;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomService\ServiceOrderRequest;
use App\Models\HotelService;
use App\Models\ServiceOrder;
use App\Services\ServiceOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceOrderController extends Controller
{
    public function index(Request $request): View
    {
        $servicePaginator = HotelService::query()->where('is_active', true)->where('is_available', true)
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();
        $services = $servicePaginator->getCollection()->groupBy('category');
        $access = $request->attributes->get('roomServiceAccess');

        return view('room-service.services.index', compact('services', 'servicePaginator', 'access'));
    }

    public function store(ServiceOrderRequest $request, ServiceOrderService $service): RedirectResponse
    {
        $order = $service->place($request->attributes->get('roomServiceAccess'), $request->validated());

        return redirect()->route('room-service.services.show', $order)->with('success', 'Permintaan layanan '.$order->order_code.' berhasil dikirim.');
    }

    public function orders(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $orders = ServiceOrder::query()->where('stay_id', $access->stay_id)->with('service')->latest()->paginate(10);

        return view('room-service.services.orders', compact('orders', 'access'));
    }

    public function show(Request $request, ServiceOrder $serviceOrder): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        abort_unless($serviceOrder->stay_id === $access->stay_id, 404);

        return view('room-service.services.show', ['order' => $serviceOrder->load(['service', 'room']), 'access' => $access]);
    }
}
