<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final readonly class ReportPeriod
{
    public function __construct(
        public string $preset,
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {}

    public static function fromRequest(Request $request, string $default = 'this_month'): self
    {
        $data = $request->validate([
            'period' => ['nullable', Rule::in(array_keys(self::options()))],
            'start_date' => ['nullable', 'required_if:period,custom', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'required_if:period,custom', 'date_format:Y-m-d'],
        ]);

        $preset = $data['period'] ?? $default;
        $today = CarbonImmutable::today();

        [$start, $end] = match ($preset) {
            'today' => [$today, $today],
            'last_month' => [$today->subMonthNoOverflow()->startOfMonth(), $today->subMonthNoOverflow()->endOfMonth()],
            'three_months' => [$today->subMonthsNoOverflow(2)->startOfMonth(), $today],
            'six_months' => [$today->subMonthsNoOverflow(5)->startOfMonth(), $today],
            'this_year' => [$today->startOfYear(), $today],
            'custom' => [
                CarbonImmutable::createFromFormat('Y-m-d', $data['start_date'])->startOfDay(),
                CarbonImmutable::createFromFormat('Y-m-d', $data['end_date'])->startOfDay(),
            ],
            default => [$today->startOfMonth(), $today],
        };

        if ($start->greaterThan($end)) {
            throw ValidationException::withMessages([
                'end_date' => 'Tanggal akhir harus sama dengan atau setelah tanggal awal.',
            ]);
        }

        if ($start->diffInDays($end) > 731) {
            throw ValidationException::withMessages([
                'end_date' => 'Rentang laporan maksimal 2 tahun.',
            ]);
        }

        return new self($preset, $start->startOfDay(), $end->endOfDay());
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return [
            'today' => 'Hari Ini',
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'three_months' => '3 Bulan',
            'six_months' => '6 Bulan',
            'this_year' => 'Tahun Ini',
            'custom' => 'Pilih Tanggal',
        ];
    }

    public function label(): string
    {
        return $this->start->translatedFormat('d M Y').' - '.$this->end->translatedFormat('d M Y');
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    public function bounds(): array
    {
        return [$this->start, $this->end];
    }
}
