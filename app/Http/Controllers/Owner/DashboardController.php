<?php

namespace App\Http\Controllers\Owner;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
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

        return view('owner.dashboard', [
            'metrics' => $this->reports->dashboardMetrics($period),
            'trend' => $this->reports->trend($period),
            'period' => $period,
            'periodOptions' => ReportPeriod::options(),
            'recentPayments' => Payment::query()->with(['reservation', 'method'])->where('status', PaymentStatus::Paid->value)
                ->latest('paid_at')->limit(8)->get(),
        ]);
    }
}
