<?php

namespace Tests\Feature;

use App\Models\HotelSetting;
use App\Models\Promotion;
use App\Models\RoomType;
use App\Models\WebsiteContent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_and_authentication_pages_render_successfully(): void
    {
        foreach ([
            'home',
            'public.rooms.index',
            'public.about',
            'public.facilities',
            'public.gallery',
            'public.promotions.index',
            'public.contact',
            'login',
            'register',
            'password.request',
        ] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_public_navigation_marks_the_current_page_as_active(): void
    {
        foreach ([
            'public.facilities' => 'Fasilitas',
            'public.promotions.index' => 'Promosi',
            'public.gallery' => 'Galeri',
            'public.about' => 'Tentang',
            'public.contact' => 'Kontak',
        ] as $routeName => $label) {
            $url = route($routeName);

            $this->get($url)
                ->assertOk()
                ->assertSee('<li class="active"><a href="'.$url.'">'.$label.'</a></li>', false);
        }
    }

    public function test_home_availability_form_uses_backend_compatible_dates(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('type="date" id="check_in"', false)
            ->assertSee('type="date" id="check_out"', false)
            ->assertDontSee('icon_calendar', false);

        $this->get(route('public.rooms.index', [
            'check_in' => today()->addDay()->format('Y-m-d'),
            'check_out' => today()->addDays(2)->format('Y-m-d'),
            'adults' => 1,
        ]))->assertOk()->assertSee('Hasil Ketersediaan');
    }

    public function test_active_room_type_detail_page_renders(): void
    {
        $roomType = RoomType::query()->create([
            'code' => 'TEST-ROOM',
            'name' => 'Test Room',
            'slug' => 'test-room',
            'capacity' => 2,
            'max_adults' => 2,
            'max_children' => 0,
            'bed_count' => 1,
            'base_price' => 500000,
            'extra_bed_price' => 0,
            'is_active' => true,
        ]);

        $this->get(route('public.rooms.show', $roomType))
            ->assertOk()
            ->assertSee('Test Room')
            ->assertSee('room-details-spaced', false);
    }

    public function test_landing_spacing_and_active_promotion_information_are_visible(): void
    {
        $promotion = Promotion::query()->create([
            'code' => 'LIBURAN20', 'name' => 'Promo Liburan', 'discount_type' => 'percent',
            'discount_value' => 20, 'minimum_transaction' => 0, 'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(10)->endOfDay(), 'used_count' => 0, 'is_active' => true,
        ]);
        Promotion::query()->create([
            'code' => 'EXPIRED20', 'name' => 'Promo Kedaluwarsa', 'discount_type' => 'percent',
            'discount_value' => 20, 'minimum_transaction' => 0, 'ends_at' => now()->subDay(),
            'used_count' => 0, 'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('home-room-showcase', false)
            ->assertSee('Kode Promo: '.$promotion->code)
            ->assertSee('Berlaku sampai '.$promotion->ends_at->translatedFormat('d M Y'));

        $this->get(route('public.about'))->assertOk()->assertSee('about-content-spaced', false);
        $this->get(route('public.promotions.index'))
            ->assertOk()->assertSee('Kode Promo: LIBURAN20')->assertDontSee('EXPIRED20');
    }

    public function test_registered_website_content_slots_are_rendered_on_their_public_pages(): void
    {
        foreach ([
            ['hero_title', 'hero', 'Judul Hero CMS', null, 'home'],
            ['about_hero', 'about', 'Tentang dari CMS', 'Deskripsi Tentang CMS', 'public.about'],
            ['rooms_hero', 'rooms', 'Kamar dari CMS', 'Deskripsi Kamar CMS', 'public.rooms.index'],
            ['facilities_hero', 'facilities', 'Fasilitas dari CMS', 'Deskripsi Fasilitas CMS', 'public.facilities'],
            ['promotions_hero', 'promotions', 'Promosi dari CMS', 'Deskripsi Promosi CMS', 'public.promotions.index'],
            ['gallery_hero', 'gallery', 'Galeri dari CMS', 'Deskripsi Galeri CMS', 'public.gallery'],
            ['contact_hero', 'contact', 'Kontak dari CMS', 'Deskripsi Kontak CMS', 'public.contact'],
        ] as [$key, $section, $title, $content, $routeName]) {
            WebsiteContent::query()->updateOrCreate(
                ['content_key' => $key],
                ['section' => $section, 'title' => $title, 'content' => $content, 'sort_order' => 1, 'is_active' => true]
            );

            $response = $this->get(route($routeName))->assertOk()->assertSee($title);
            if ($content) {
                $response->assertSee($content);
            }
        }

        WebsiteContent::query()->updateOrCreate(
            ['content_key' => 'footer_summary'],
            ['section' => 'footer', 'content' => 'Footer dinamis dari CMS', 'sort_order' => 1, 'is_active' => true]
        );
        HotelSetting::query()->updateOrCreate(
            ['setting_key' => 'hotel.name'],
            ['setting_group' => 'general', 'setting_value' => 'Nama Resort Dinamis', 'value_type' => 'string']
        );

        $this->get(route('home'))->assertOk()->assertSee('Footer dinamis dari CMS')->assertSee('Nama Resort Dinamis');
    }
}
