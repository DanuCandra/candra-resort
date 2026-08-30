<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\MidtransPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransPaymentService $service): JsonResponse
    {
        try {
            $payment = $service->handleNotification($request->all());
        } catch (ValidationException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json(['message' => 'Notification processed.', 'payment_code' => $payment->payment_code]);
    }
}
