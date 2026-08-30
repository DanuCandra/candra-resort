<?php

namespace App\Http\Controllers\RoomService;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomService\FoodOrderRequest;
use App\Models\FoodOrder;
use App\Models\MenuItem;
use App\Services\FoodOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FoodOrderController extends Controller
{
    public function index(Request $request): View
    {
        $menuItems = MenuItem::query()->with('category')
            ->where('is_active', true)->where('is_available', true)
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();
        $categories = $menuItems->getCollection()->groupBy(fn (MenuItem $item): string => $item->category?->name ?? 'Menu Lainnya');
        $access = $request->attributes->get('roomServiceAccess');

        return view('room-service.food.index', compact('categories', 'menuItems', 'access'));
    }

    public function store(FoodOrderRequest $request, FoodOrderService $service): RedirectResponse
    {
        $order = $service->place($request->attributes->get('roomServiceAccess'), $request->validated());

        return redirect()->route('room-service.food.show', $order)->with('success', 'Pesanan '.$order->order_code.' berhasil dikirim ke Receptionist.');
    }

    public function orders(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $orders = FoodOrder::query()->where('stay_id', $access->stay_id)->with('items')->latest('ordered_at')->paginate(10);

        return view('room-service.food.orders', compact('orders', 'access'));
    }

    public function show(Request $request, FoodOrder $foodOrder): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        abort_unless($foodOrder->stay_id === $access->stay_id, 404);
        $foodOrder->load(['items', 'room']);

        return view('room-service.food.show', ['order' => $foodOrder, 'access' => $access]);
    }
}
