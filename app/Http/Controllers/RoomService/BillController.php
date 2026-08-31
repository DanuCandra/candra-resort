<?php

namespace App\Http\Controllers\RoomService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Menampilkan tagihan berjalan kepada tamu.
class BillController extends Controller
{
    public function __invoke(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $access->load(['room', 'stay.folio']);
        $folio = $access->stay->folio;
        $categoryTotals = $folio?->items()
            ->where('is_void', false)
            ->reorder()
            ->selectRaw('item_type, SUM(amount) as total')
            ->groupBy('item_type')
            ->pluck('total', 'item_type') ?? collect();
        $items = $folio?->items()
            ->where('is_void', false)
            ->latest('posted_at')
            ->paginate(12, ['*'], 'items_page')
            ->withQueryString();
        $payments = $folio?->payments()
            ->with('method')
            ->latest()
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString();

        return view('room-service.bill.show', [
            'access' => $access,
            'folio' => $folio,
            'categoryTotals' => $categoryTotals,
            'items' => $items,
            'payments' => $payments,
        ]);
    }
}
