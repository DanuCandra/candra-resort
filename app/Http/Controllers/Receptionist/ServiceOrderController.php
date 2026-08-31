<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\ServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\ServiceOrderStatusRequest;
use App\Models\ServiceOrder;
use App\Services\ServiceOrderService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Memantau dan memperbarui pesanan layanan hotel.
class ServiceOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = ServiceOrder::query()->with(['service', 'room', 'stay', 'handledBy'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested->where('order_code', 'like', '%'.$request->string('search').'%')->orWhereHas('stay', fn ($stay) => $stay->where('guest_name', 'like', '%'.$request->string('search').'%'))))
            ->orderByRaw("CASE status WHEN 'requested' THEN 1 WHEN 'accepted' THEN 2 WHEN 'scheduled' THEN 3 WHEN 'processing' THEN 4 ELSE 5 END")
            ->latest()->paginate(15)->withQueryString();

        return view('receptionist.service-orders.index', ['orders' => $orders, 'statuses' => ServiceOrderStatus::cases()]);
    }

    public function show(ServiceOrder $serviceOrder): View
    {
        return view('receptionist.service-orders.show', ['order' => $serviceOrder->load(['service', 'room', 'stay.folio', 'handledBy'])]);
    }

    public function updateStatus(ServiceOrderStatusRequest $request, ServiceOrder $serviceOrder, ServiceOrderService $service): RedirectResponse
    {
        $old = $serviceOrder->status->value;
        $status = ServiceOrderStatus::from($request->validated('status'));
        $order = $service->transition($serviceOrder, $status, $request->user(), $request->validated('scheduled_at'));
        AuditLogger::record($request, 'status_change', 'service_orders', $order, 'Mengubah status layanan '.$order->order_code.' dari '.$old.' menjadi '.$status->value.'.');

        return back()->with('success', 'Status layanan diperbarui menjadi '.$status->label().'.');
    }
}
