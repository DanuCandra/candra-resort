<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\GalleryImage;
use App\Models\HotelSetting;
use App\Models\Promotion;
use App\Models\RoomType;
use App\Models\WebsiteContent;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('public.home', [
            'settings' => HotelSetting::query()->pluck('setting_value', 'setting_key'),
            'contents' => WebsiteContent::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->keyBy('content_key'),
            'roomTypes' => RoomType::query()
                ->where('is_active', true)
                ->with(['images', 'facilities'])
                ->withCount(['rooms' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
            'facilities' => Facility::query()
                ->where('is_active', true)
                ->whereIn('scope', ['hotel', 'both'])
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
            'promotions' => Promotion::query()
                ->where('is_active', true)
                ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
                ->latest()
                ->limit(3)
                ->get(),
            'galleryImages' => GalleryImage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
        ]);
    }
}
