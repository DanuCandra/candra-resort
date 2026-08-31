<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\GalleryImage;
use App\Models\HotelSetting;
use App\Models\Promotion;
use App\Models\WebsiteContent;
use Illuminate\View\View;

// Menampilkan halaman informasi publik hotel.
class PageController extends Controller
{
    public function about(): View
    {
        return view('public.about', [
            'contents' => WebsiteContent::query()->whereIn('section', ['about', 'policy'])->where('is_active', true)
                ->orderBy('sort_order')->get()->keyBy('content_key'),
        ]);
    }

    public function facilities(): View
    {
        return view('public.facilities', [
            'content' => $this->content('facilities_hero'),
            'facilities' => Facility::query()->where('is_active', true)->whereIn('scope', ['hotel', 'both'])
                ->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function gallery(): View
    {
        return view('public.gallery', [
            'content' => $this->content('gallery_hero'),
            'images' => GalleryImage::query()->where('is_active', true)->orderBy('sort_order')->paginate(12),
        ]);
    }

    public function promotions(): View
    {
        return view('public.promotions.index', [
            'content' => $this->content('promotions_hero'),
            'promotions' => Promotion::query()
                ->where('is_active', true)
                ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
                ->latest()
                ->paginate(9),
        ]);
    }

    public function contact(): View
    {
        return view('public.contact', [
            'settings' => HotelSetting::query()->pluck('setting_value', 'setting_key'),
            'contents' => WebsiteContent::query()->where('section', 'contact')->where('is_active', true)
                ->orderBy('sort_order')->get()->keyBy('content_key'),
        ]);
    }

    private function content(string $key): ?WebsiteContent
    {
        return WebsiteContent::query()->where('content_key', $key)->where('is_active', true)->first();
    }
}
