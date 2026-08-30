<?php

namespace App\Http\Controllers\RoomService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function __invoke(Request $request): View
    {
        $access = $request->attributes->get('roomServiceAccess');
        $access->load(['room', 'stay.folio']);
        $folio = $access->stay->folio;
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
            'items' => $items,
            'payments' => $payments,
        ]);
    }
}
