<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\GalleryImage;
use App\Models\HotelSetting;
use App\Models\Promotion;
use App\Models\WebsiteContent;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('public.about', [
            'contents' => WebsiteContent::query()->where('section', 'about')->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function facilities(): View
    {
        return view('public.facilities', [
            'facilities' => Facility::query()->where('is_active', true)->whereIn('scope', ['hotel', 'both'])->orderBy('sort_order')->get(),
        ]);
    }

    public function gallery(): View
    {
        return view('public.gallery', [
            'images' => GalleryImage::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function promotions(): View
    {
        return view('public.promotions.index', [
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
        ]);
    }
}
