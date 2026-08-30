<?php

use App\Services\ReservationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(fn (): int => app(ReservationService::class)->expirePendingReservations())
    ->everyMinute()
    ->name('expire-pending-reservations')
    ->withoutOverlapping();
