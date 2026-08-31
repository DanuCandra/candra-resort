<?php

namespace App\Providers;

use App\Contracts\MidtransGateway;
use App\Models\HotelSetting;
use App\Models\WebsiteContent;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

// Mendaftarkan layanan dan konfigurasi global aplikasi.
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MidtransGateway::class, MidtransService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Paginator::useBootstrapFive();
        View::composer('layouts.guest', function ($view): void {
            $view->with('siteSettings', HotelSetting::query()->pluck('setting_value', 'setting_key'));
            $view->with('siteContents', WebsiteContent::query()
                ->where('section', 'footer')
                ->where('is_active', true)
                ->get()
                ->keyBy('content_key'));
        });
    }
}
