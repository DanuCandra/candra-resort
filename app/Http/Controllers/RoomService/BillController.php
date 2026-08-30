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
        $access->load(['room', 'stay.folio.items', 'stay.folio.payments.method']);

        return view('room-service.bill.show', [
            'access' => $access,
            'folio' => $access->stay->folio,
        ]);
    }
}
