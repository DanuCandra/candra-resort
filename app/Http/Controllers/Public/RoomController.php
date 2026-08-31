<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\WebsiteContent;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Menampilkan kamar publik beserta harga dan ketersediaannya.
class RoomController extends Controller
{
    public function index(Request $request, AvailabilityService $availability, PricingService $pricing): View
    {
        $search = null;
        if ($request->filled('check_in') || $request->filled('check_out')) {
            $search = $request->validate([
                'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
                'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
                'adults' => ['nullable', 'integer', 'min:1', 'max:50'],
                'children' => ['nullable', 'integer', 'min:0', 'max:50'],
                'promo_code' => ['nullable', 'string', 'max:100'],
            ]);
            $search['adults'] ??= 1;
            $search['children'] ??= 0;
        }

        $roomTypes = RoomType::query()
            ->where('is_active', true)
            ->with(['images', 'facilities'])
            ->withCount(['rooms' => fn ($query) => $query->where('is_active', true)])
            ->when($search, fn ($query) => $query
                ->where('max_adults', '>=', $search['adults'])
                ->where('max_children', '>=', $search['children']))
            ->orderBy('sort_order')
            ->paginate(9);

        if ($search) {
            $checkIn = CarbonImmutable::parse($search['check_in']);
            $checkOut = CarbonImmutable::parse($search['check_out']);
            $roomTypes->through(function (RoomType $roomType) use ($availability, $pricing, $checkIn, $checkOut): RoomType {
                $roomType->setAttribute('available_rooms', $availability->availableCount($roomType, $checkIn, $checkOut));
                $roomType->setAttribute('search_quote', $pricing->quote($roomType, $checkIn, $checkOut));

                return $roomType;
            });
        }

        $content = WebsiteContent::query()->where('content_key', 'rooms_hero')->where('is_active', true)->first();

        return view('public.rooms.index', compact('roomTypes', 'search', 'content'));
    }

    public function show(Request $request, RoomType $roomType, AvailabilityService $availability, PricingService $pricing): View
    {
        abort_unless($roomType->is_active, 404);

        $roomType->load(['images', 'facilities'])
            ->loadCount(['rooms' => fn ($query) => $query->where('is_active', true)]);

        $search = null;
        $quote = null;
        $availableRooms = null;
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $search = $request->validate([
                'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
                'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
                'adults' => ['nullable', 'integer', 'min:1', 'max:'.$roomType->max_adults],
                'children' => ['nullable', 'integer', 'min:0', 'max:'.$roomType->max_children],
                'promo_code' => ['nullable', 'string', 'max:100'],
            ]);
            $search['adults'] ??= 1;
            $search['children'] ??= 0;
            $checkIn = CarbonImmutable::parse($search['check_in']);
            $checkOut = CarbonImmutable::parse($search['check_out']);
            $availableRooms = $availability->availableCount($roomType, $checkIn, $checkOut);
            $quote = $pricing->quote($roomType, $checkIn, $checkOut, $search['promo_code'] ?? null, $request->user());
        }

        return view('public.rooms.show', compact('roomType', 'search', 'quote', 'availableRooms'));
    }
}
