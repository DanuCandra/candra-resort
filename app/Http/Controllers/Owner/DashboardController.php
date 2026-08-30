<?php

namespace App\Http\Controllers\Owner;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\OwnerReportService;
use App\Support\ReportPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly OwnerReportService $reports) {}

    public function __invoke(Request $request): View
    {
        $period = ReportPeriod::fromRequest($request);
        $periodDays = (int) $period->start->startOfDay()->diffInDays($period->end->startOfDay()) + 1;
        $previousEnd = $period->start->subDay()->endOfDay();
        $previousPeriod = new ReportPeriod(
            'comparison',
            $previousEnd->subDays($periodDays - 1)->startOfDay(),
            $previousEnd,
        );
        $paymentMethodSummary = Payment::query()
            ->selectRaw('payment_method_id, COUNT(*) as transaction_count, COALESCE(SUM(amount), 0) as total_amount')
            ->with('method:id,name')
            ->where('status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', $period->bounds())
            ->groupBy('payment_method_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        return view('owner.dashboard', [
            'metrics' => $this->reports->dashboardMetrics($period),
            'previousMetrics' => $this->reports->dashboardMetrics($previousPeriod),
            'trend' => $this->reports->trend($period),
            'period' => $period,
            'previousPeriod' => $previousPeriod,
            'periodOptions' => ReportPeriod::options(),
            'paymentMethodSummary' => $paymentMethodSummary,
            'reservationSources' => Reservation::query()
                ->whereBetween('created_at', $period->bounds())
                ->selectRaw('source, COUNT(*) as total')
                ->groupBy('source')
                ->pluck('total', 'source'),
            'serviceSummary' => $this->reports->serviceSummary($period),
            'recentPayments' => Payment::query()->with(['reservation', 'method'])->where('status', PaymentStatus::Paid->value)
                ->latest('paid_at')->limit(8)->get(),
        ]);
    }
}
