<?php

namespace App\Http\Controllers\Receptionist;

use App\Enums\FoodOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\FoodOrderStatusRequest;
use App\Models\FoodOrder;
use App\Services\FoodOrderService;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Memantau dan memperbarui pesanan makanan tamu.
class FoodOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = FoodOrder::query()->with(['room', 'stay', 'handledBy'])->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($nested) => $nested
                ->where('order_code', 'like', '%'.$request->string('search').'%')
                ->orWhereHas('stay', fn ($stay) => $stay->where('guest_name', 'like', '%'.$request->string('search').'%'))))
            ->orderByRaw("CASE status WHEN 'requested' THEN 1 WHEN 'accepted' THEN 2 WHEN 'processing' THEN 3 ELSE 4 END")
            ->latest('ordered_at')->paginate(15)->withQueryString();

        return view('receptionist.food.orders.index', ['orders' => $orders, 'statuses' => FoodOrderStatus::cases()]);
    }

    public function show(FoodOrder $foodOrder): View
    {
        $foodOrder->load(['items.menuItem', 'room', 'stay.folio', 'handledBy']);

        return view('receptionist.food.orders.show', ['order' => $foodOrder]);
    }

    public function updateStatus(FoodOrderStatusRequest $request, FoodOrder $foodOrder, FoodOrderService $service): RedirectResponse
    {
        $oldStatus = $foodOrder->status->value;
        $status = FoodOrderStatus::from($request->validated('status'));
        $order = $service->transition($foodOrder, $status, $request->user());
        AuditLogger::record($request, 'status_change', 'food_orders', $order, 'Mengubah status pesanan '.$order->order_code.' dari '.$oldStatus.' menjadi '.$status->value.'.', ['status' => $oldStatus], ['status' => $status->value]);

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi '.$status->label().'.');
    }
}
