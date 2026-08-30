<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use App\Models\HotelSetting;
use App\Models\User;
use App\Models\WebsiteContent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReceptionistWebsiteContentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_receptionist_can_open_website_content_and_update_hotel_information(): void
    {
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->get(route('receptionist.website.index'))
            ->assertOk()->assertSee('Informasi Hotel')->assertSee('Konten Halaman')->assertSee('Galeri');

        $this->actingAs($receptionist)->get(route('receptionist.website.index', ['tab' => 'contents', 'page' => 'facilities']))
            ->assertOk()
            ->assertSee('Pilih Halaman yang Diedit')
            ->assertSee('Banner Halaman Fasilitas')
            ->assertSee('Lihat posisi');

        $this->actingAs($receptionist)->put(route('receptionist.website.settings.update'), [
            'hotel_name' => 'Candra Resort Test',
            'hotel_phone' => '+62 811 2222 3333',
            'hotel_email' => 'hotel-test@example.com',
            'hotel_address' => 'Alamat Candra Resort Test',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
        ])->assertRedirect(route('receptionist.website.index', ['tab' => 'settings']));

        $this->assertSame('Candra Resort Test', HotelSetting::query()->where('setting_key', 'hotel.name')->value('setting_value'));
        $this->assertDatabaseHas('audit_logs', ['user_id' => $receptionist->id, 'module' => 'hotel_settings', 'event' => 'update']);
    }

    public function test_receptionist_can_manage_page_content_and_gallery(): void
    {
        Storage::fake('public');
        $receptionist = User::factory()->receptionist()->create();

        $this->actingAs($receptionist)->post(route('receptionist.website.contents.store'), [
            'section' => 'about', 'content_key' => 'about_test_content',
            'title' => 'Judul Konten Test', 'content' => 'Isi konten website test.',
            'sort_order' => 20, 'is_active' => 1,
            'image' => UploadedFile::fake()->image('content.jpg', 1200, 800),
        ])->assertRedirect(route('receptionist.website.index', ['tab' => 'contents']));

        $content = WebsiteContent::query()->where('content_key', 'about_test_content')->firstOrFail();
        Storage::disk('public')->assertExists($content->image_path);

        $this->actingAs($receptionist)->put(route('receptionist.website.contents.update', $content), [
            'section' => 'about', 'content_key' => 'about_test_content',
            'title' => 'Judul Diperbarui', 'content' => 'Isi diperbarui.',
            'sort_order' => 21, 'is_active' => 1,
        ])->assertRedirect(route('receptionist.website.index', ['tab' => 'contents']));
        $this->assertDatabaseHas('website_contents', ['id' => $content->id, 'title' => 'Judul Diperbarui']);

        $oldImage = $content->image_path;
        $this->actingAs($receptionist)->put(route('receptionist.website.contents.update', $content), [
            'section' => 'about', 'content_key' => 'about_test_content',
            'title' => 'Judul Diperbarui', 'content' => 'Isi diperbarui.',
            'sort_order' => 21, 'is_active' => 1, 'remove_image' => 1,
        ])->assertRedirect(route('receptionist.website.index', ['tab' => 'contents']));
        Storage::disk('public')->assertMissing($oldImage);
        $this->assertNull($content->fresh()->image_path);

        $this->actingAs($receptionist)->post(route('receptionist.website.gallery.store'), [
            'caption' => 'Galeri Test', 'alt_text' => 'Foto galeri test',
            'sort_order' => 10, 'is_active' => 1,
            'image' => UploadedFile::fake()->image('gallery.jpg', 1200, 800),
        ])->assertRedirect(route('receptionist.website.index', ['tab' => 'gallery']));

        $gallery = GalleryImage::query()->where('caption', 'Galeri Test')->firstOrFail();
        Storage::disk('public')->assertExists($gallery->image_path);

        $this->actingAs($receptionist)->delete(route('receptionist.website.gallery.destroy', $gallery))
            ->assertRedirect(route('receptionist.website.index', ['tab' => 'gallery']));
        $this->assertSoftDeleted($gallery);
    }

    public function test_owner_cannot_manage_receptionist_website_content(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get(route('receptionist.website.index'))->assertForbidden();
        $this->actingAs($owner)->put(route('receptionist.website.settings.update'), [])->assertForbidden();
    }
}
