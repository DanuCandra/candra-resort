<?php

namespace App\Http\Controllers\Owner;

use App\Enums\FoodOrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ServiceOrder;
use App\Services\OwnerReportService;
use App\Support\ReportPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const TYPES = ['reservations', 'occupancy', 'revenue', 'payments', 'services', 'monthly'];

    public function __construct(private readonly OwnerReportService $reports) {}

    public function reservations(Request $request): View
    {
        return $this->render($request, 'reservations');
    }

    public function occupancy(Request $request): View
    {
        return $this->render($request, 'occupancy');
    }

    public function revenue(Request $request): View
    {
        return $this->render($request, 'revenue');
    }

    public function payments(Request $request): View
    {
        return $this->render($request, 'payments');
    }

    public function services(Request $request): View
    {
        return $this->render($request, 'services');
    }

    public function monthly(Request $request): View
    {
        return $this->render($request, 'monthly', 'this_year');
    }

    public function export(Request $request, string $report): StreamedResponse
    {
        abort_unless(in_array($report, self::TYPES, true), 404);
        $period = ReportPeriod::fromRequest($request, $report === 'monthly' ? 'this_year' : 'this_month');
        [$headers, $rows] = $this->csvData($report, $period);
        $filename = "laporan-{$report}-{$period->start->format('Ymd')}-{$period->end->format('Ymd')}.csv";

        return response()->streamDownload(function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers, ';');
            foreach ($rows as $row) {
                fputcsv($stream, $row, ';');
            }
            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function render(Request $request, string $type, string $defaultPeriod = 'this_month'): View
    {
        $period = ReportPeriod::fromRequest($request, $defaultPeriod);
        $data = match ($type) {
            'reservations' => $this->reservationData($period),
            'occupancy' => $this->occupancyData($period),
            'revenue' => $this->revenueData($period),
            'payments' => $this->paymentData($period),
            'services' => $this->serviceData($period),
            'monthly' => $this->monthlyData($period),
        };

        return view('owner.reports.show', array_merge($data, [
            'type' => $type,
            'period' => $period,
            'periodOptions' => ReportPeriod::options(),
        ]));
    }

    /** @return array<string, mixed> */
    private function reservationData(ReportPeriod $period): array
    {
        $base = Reservation::query()->whereBetween('created_at', $period->bounds());
        $summary = (clone $base)->get(['status', 'source', 'grand_total']);

        return [
            'title' => 'Laporan Reservasi',
            'description' => 'Reservasi berdasarkan tanggal pemesanan dibuat.',
            'rows' => (clone $base)->with(['roomType:id,name', 'room:id,room_number'])->latest()->paginate(15)->withQueryString(),
            'metrics' => [
                'total' => $summary->count(),
                'online' => $summary->where('source', 'online')->count(),
                'walk_in' => $summary->where('source', 'walk_in')->count(),
                'value' => (float) $summary->sum('grand_total'),
            ],
            'statusSummary' => $summary->countBy(fn (Reservation $reservation): string => $reservation->status->value),
            'trend' => $this->reports->trend($period),
        ];
    }

    /** @return array<string, mixed> */
    private function occupancyData(ReportPeriod $period): array
    {
        $occupancy = $this->reports->occupancy($period);

        return [
            'title' => 'Laporan Okupansi',
            'description' => 'Okupansi aktual berdasarkan masa inap yang telah check-in.',
            'occupancy' => $occupancy,
            'rows' => collect($occupancy['daily'])->reverse()->values(),
        ];
    }

    /** @return array<string, mixed> */
    private function revenueData(ReportPeriod $period): array
    {
        $base = Payment::query()->where('status', PaymentStatus::Paid->value)->whereBetween('paid_at', $period->bounds());
        $summary = (clone $base)->get(['amount', 'source', 'paid_at']);

        return [
            'title' => 'Laporan Pendapatan',
            'description' => 'Arus kas masuk dari pembayaran yang telah terverifikasi lunas.',
            'rows' => (clone $base)->with(['reservation:id,booking_code,guest_name', 'method:id,name'])
                ->latest('paid_at')->paginate(15)->withQueryString(),
            'metrics' => [
                'total' => (float) $summary->sum('amount'),
                'transactions' => $summary->count(),
                'average' => $summary->count() > 0 ? round((float) $summary->avg('amount')) : 0,
                'midtrans' => (float) $summary->where('source', 'midtrans')->sum('amount'),
            ],
            'trend' => $this->reports->trend($period),
        ];
    }

    /** @return array<string, mixed> */
    private function paymentData(ReportPeriod $period): array
    {
        $base = Payment::query()->whereBetween('created_at', $period->bounds());
        $summary = (clone $base)->with('method:id,name')->get();

        return [
            'title' => 'Laporan Pembayaran',
            'description' => 'Seluruh upaya pembayaran berdasarkan tanggal transaksi dibuat.',
            'rows' => (clone $base)->with(['reservation:id,booking_code,guest_name', 'method:id,name'])
                ->latest()->paginate(15)->withQueryString(),
            'metrics' => [
                'total' => $summary->count(),
                'paid' => $summary->where('status', PaymentStatus::Paid)->count(),
                'pending' => $summary->where('status', PaymentStatus::Pending)->count(),
                'paid_amount' => (float) $summary->where('status', PaymentStatus::Paid)->sum('amount'),
            ],
            'statusSummary' => $summary->countBy(fn (Payment $payment): string => $payment->status->value),
            'methodSummary' => $summary->where('status', PaymentStatus::Paid)
                ->groupBy(fn (Payment $payment): string => $payment->method?->name ?? 'Tidak diketahui')
                ->map(fn (Collection $payments): float => (float) $payments->sum('amount')),
        ];
    }

    /** @return array<string, mixed> */
    private function serviceData(ReportPeriod $period): array
    {
        $foodOrders = FoodOrder::query()->with('room:id,room_number')
            ->where('status', FoodOrderStatus::Completed->value)->whereBetween('completed_at', $period->bounds())
            ->latest('completed_at')->get();
        $serviceOrders = ServiceOrder::query()->with(['room:id,room_number', 'service:id,name'])
            ->where('status', ServiceOrderStatus::Completed->value)->whereBetween('completed_at', $period->bounds())
            ->latest('completed_at')->get();

        return [
            'title' => 'Laporan Layanan',
            'description' => 'Nilai pesanan F&B dan layanan yang telah diselesaikan.',
            'foodOrders' => $foodOrders,
            'serviceOrders' => $serviceOrders,
            'serviceSummary' => $this->reports->serviceSummary($period),
            'metrics' => [
                'food_orders' => $foodOrders->count(),
                'food_amount' => (float) $foodOrders->sum('total_amount'),
                'service_orders' => $serviceOrders->count(),
                'service_amount' => (float) $serviceOrders->sum('total_amount'),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function monthlyData(ReportPeriod $period): array
    {
        return [
            'title' => 'Laporan Bulanan',
            'description' => 'Perbandingan reservasi, pendapatan kas, dan okupansi per bulan.',
            'rows' => $this->reports->monthlySummary($period),
        ];
    }

    /** @return array{0: array<int, string>, 1: iterable<int, array<int, int|float|string|null>>} */
    private function csvData(string $type, ReportPeriod $period): array
    {
        return match ($type) {
            'reservations' => [
                ['Kode', 'Tanggal Dibuat', 'Tamu', 'Sumber', 'Check-in', 'Check-out', 'Status', 'Total'],
                Reservation::query()->whereBetween('created_at', $period->bounds())->orderBy('created_at')->get()
                    ->map(fn (Reservation $row): array => [$row->booking_code, $row->created_at->format('Y-m-d H:i'), $row->guest_name, $row->source, $row->check_in_date->format('Y-m-d'), $row->check_out_date->format('Y-m-d'), $row->status->value, (float) $row->grand_total]),
            ],
            'occupancy' => [
                ['Tanggal', 'Kamar Terisi', 'Kapasitas Kamar', 'Okupansi (%)'],
                collect($this->reports->occupancy($period)['daily'])->map(fn (array $row): array => [$row['date']->format('Y-m-d'), $row['occupied'], $row['capacity'], $row['rate']]),
            ],
            'revenue' => [
                ['Kode', 'Tanggal Lunas', 'Metode', 'Sumber', 'Tujuan', 'Jumlah'],
                Payment::query()->with('method:id,name')->where('status', PaymentStatus::Paid->value)
                    ->whereBetween('paid_at', $period->bounds())->orderBy('paid_at')->get()
                    ->map(fn (Payment $row): array => [$row->payment_code, $row->paid_at?->format('Y-m-d H:i'), $row->method?->name, $row->source, $row->purpose, (float) $row->amount]),
            ],
            'payments' => [
                ['Kode', 'Tanggal Dibuat', 'Metode', 'Sumber', 'Status', 'Jumlah'],
                Payment::query()->with('method:id,name')->whereBetween('created_at', $period->bounds())->orderBy('created_at')->get()
                    ->map(fn (Payment $row): array => [$row->payment_code, $row->created_at->format('Y-m-d H:i'), $row->method?->name, $row->source, $row->status->value, (float) $row->amount]),
            ],
            'services' => [
                ['Jenis', 'Kode', 'Layanan', 'Kamar', 'Tanggal Selesai', 'Jumlah'],
                $this->serviceCsvRows($period),
            ],
            'monthly' => [
                ['Bulan', 'Reservasi', 'Pembatalan', 'Pendapatan', 'Room-night Terisi', 'Room-night Tersedia', 'Okupansi (%)'],
                $this->reports->monthlySummary($period)->map(fn (array $row): array => [$row['month'], $row['reservations'], $row['cancelled'], $row['revenue'], $row['occupied_room_nights'], $row['capacity_room_nights'], $row['occupancy_rate']]),
            ],
        };
    }

    /** @return Collection<int, array<int, int|float|string|null>> */
    private function serviceCsvRows(ReportPeriod $period): Collection
    {
        $food = FoodOrder::query()->with('room:id,room_number')->where('status', FoodOrderStatus::Completed->value)
            ->whereBetween('completed_at', $period->bounds())->get()
            ->map(fn (FoodOrder $row): array => ['F&B', $row->order_code, 'Makanan & Minuman', $row->room?->room_number, $row->completed_at?->format('Y-m-d H:i'), (float) $row->total_amount]);
        $services = ServiceOrder::query()->with(['room:id,room_number', 'service:id,name'])
            ->where('status', ServiceOrderStatus::Completed->value)->whereBetween('completed_at', $period->bounds())->get()
            ->map(fn (ServiceOrder $row): array => ['Layanan', $row->order_code, $row->service?->name, $row->room?->room_number, $row->completed_at?->format('Y-m-d H:i'), (float) $row->total_amount]);

        return $food->concat($services)->values();
    }
}
