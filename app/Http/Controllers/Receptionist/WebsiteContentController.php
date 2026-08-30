<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\GalleryImageRequest;
use App\Http\Requests\Receptionist\WebsiteContentRequest;
use App\Http\Requests\Receptionist\WebsiteSettingsRequest;
use App\Models\GalleryImage;
use App\Models\HotelSetting;
use App\Models\WebsiteContent;
use App\Support\AuditLogger;
use App\Support\WebsiteContentRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebsiteContentController extends Controller
{
    public function index(Request $request): View
    {
        $pages = WebsiteContentRegistry::pages();
        $slots = WebsiteContentRegistry::slots();
        $selectedPage = array_key_exists((string) $request->query('page'), $pages)
            ? (string) $request->query('page')
            : 'home';
        $selectedSlots = collect($slots)->filter(fn (array $slot): bool => $slot['page'] === $selectedPage);
        $registeredContents = WebsiteContent::query()
            ->whereIn('content_key', array_keys($slots))
            ->get()
            ->keyBy('content_key');
        $customContents = WebsiteContent::query()
            ->whereNotIn('content_key', array_keys($slots))
            ->whereIn('section', $pages[$selectedPage]['sections'])
            ->orderBy('section')
            ->orderBy('sort_order')
            ->paginate(6, ['*'], 'contents_page')
            ->withQueryString();

        return view('receptionist.website.index', [
            'settings' => HotelSetting::query()->pluck('setting_value', 'setting_key'),
            'pages' => $pages,
            'selectedPage' => $selectedPage,
            'selectedSlots' => $selectedSlots,
            'registeredContents' => $registeredContents,
            'customContents' => $customContents,
            'galleryImages' => GalleryImage::query()->orderBy('sort_order')->latest()
                ->paginate(9, ['*'], 'gallery_page')->withQueryString(),
            'metrics' => [
                'settings' => HotelSetting::query()->count(),
                'contents' => WebsiteContent::query()->count(),
                'gallery' => GalleryImage::query()->count(),
                'active_contents' => WebsiteContent::query()->where('is_active', true)->count(),
            ],
            'activeTab' => in_array($request->query('tab'), ['settings', 'contents', 'gallery'], true)
                ? $request->query('tab')
                : 'settings',
        ]);
    }

    public function updateSettings(WebsiteSettingsRequest $request): RedirectResponse
    {
        $mapping = [
            'hotel_name' => ['general', 'hotel.name', 'string', 'Nama hotel'],
            'hotel_phone' => ['contact', 'hotel.phone', 'string', 'Nomor telepon utama'],
            'hotel_email' => ['contact', 'hotel.email', 'string', 'Email utama'],
            'hotel_address' => ['contact', 'hotel.address', 'string', 'Alamat hotel'],
            'hotel_tagline' => ['general', 'hotel.tagline', 'string', 'Tagline singkat hotel'],
            'hotel_whatsapp' => ['contact', 'hotel.whatsapp', 'string', 'Nomor WhatsApp reservasi'],
            'instagram_url' => ['social', 'social.instagram', 'string', 'Tautan Instagram'],
            'facebook_url' => ['social', 'social.facebook', 'string', 'Tautan Facebook'],
            'check_in_time' => ['operation', 'hotel.check_in_time', 'time', 'Waktu check-in standar'],
            'check_out_time' => ['operation', 'hotel.check_out_time', 'time', 'Waktu check-out standar'],
        ];

        DB::transaction(function () use ($request, $mapping): void {
            foreach ($mapping as $field => [$group, $key, $type, $description]) {
                $setting = HotelSetting::query()->firstOrNew(['setting_key' => $key]);
                $old = $setting->exists ? $setting->toArray() : null;
                $setting->fill([
                    'setting_group' => $group,
                    'setting_value' => $request->validated($field),
                    'value_type' => $type,
                    'description' => $description,
                    'updated_by' => $request->user()->id,
                ])->save();
                AuditLogger::record($request, 'update', 'hotel_settings', $setting, "Memperbarui pengaturan {$key}.", $old, $setting->toArray());
            }
        });

        return redirect()->route('receptionist.website.index', ['tab' => 'settings'])->with('success', 'Informasi hotel berhasil diperbarui.');
    }

    public function storeContent(WebsiteContentRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'remove_image']);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('website-contents', 'public');
        }
        $content = WebsiteContent::query()->create([...$data, 'updated_by' => $request->user()->id]);
        AuditLogger::record($request, 'create', 'website_contents', $content, 'Membuat konten website '.$content->content_key.'.', null, $content->toArray());

        return $this->contentRedirect($request)->with('success', 'Konten website berhasil ditambahkan.');
    }

    public function updateContent(WebsiteContentRequest $request, WebsiteContent $websiteContent): RedirectResponse
    {
        $old = $websiteContent->toArray();
        $data = $request->safe()->except(['image', 'remove_image']);
        if ($request->hasFile('image')) {
            $oldImage = $websiteContent->image_path;
            $data['image_path'] = $request->file('image')->store('website-contents', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        } elseif ($request->validated('remove_image') && $websiteContent->image_path) {
            Storage::disk('public')->delete($websiteContent->image_path);
            $data['image_path'] = null;
        }
        $websiteContent->update([...$data, 'updated_by' => $request->user()->id]);
        AuditLogger::record($request, 'update', 'website_contents', $websiteContent, 'Memperbarui konten website '.$websiteContent->content_key.'.', $old, $websiteContent->fresh()->toArray());

        return $this->contentRedirect($request)->with('success', 'Konten website berhasil diperbarui.');
    }

    public function destroyContent(Request $request, WebsiteContent $websiteContent): RedirectResponse
    {
        $key = $websiteContent->content_key;
        $websiteContent->delete();
        AuditLogger::record($request, 'delete', 'website_contents', $websiteContent, 'Menghapus konten website '.$key.'.');

        return $this->contentRedirect($request)->with('success', 'Konten website berhasil dihapus.');
    }

    public function storeGallery(GalleryImageRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['image_path'] = $request->file('image')->store('gallery', 'public');
        $image = GalleryImage::query()->create([...$data, 'updated_by' => $request->user()->id]);
        AuditLogger::record($request, 'create', 'gallery_images', $image, 'Menambahkan gambar galeri.', null, $image->toArray());

        return redirect()->route('receptionist.website.index', ['tab' => 'gallery'])->with('success', 'Gambar galeri berhasil ditambahkan.');
    }

    public function updateGallery(GalleryImageRequest $request, GalleryImage $galleryImage): RedirectResponse
    {
        $old = $galleryImage->toArray();
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $oldImage = $galleryImage->image_path;
            $data['image_path'] = $request->file('image')->store('gallery', 'public');
            Storage::disk('public')->delete($oldImage);
        }
        $galleryImage->update([...$data, 'updated_by' => $request->user()->id]);
        AuditLogger::record($request, 'update', 'gallery_images', $galleryImage, 'Memperbarui gambar galeri.', $old, $galleryImage->fresh()->toArray());

        return redirect()->route('receptionist.website.index', ['tab' => 'gallery'])->with('success', 'Gambar galeri berhasil diperbarui.');
    }

    public function destroyGallery(Request $request, GalleryImage $galleryImage): RedirectResponse
    {
        $galleryImage->delete();
        AuditLogger::record($request, 'delete', 'gallery_images', $galleryImage, 'Menghapus gambar dari galeri website.');

        return redirect()->route('receptionist.website.index', ['tab' => 'gallery'])->with('success', 'Gambar galeri berhasil dihapus.');
    }

    private function contentRedirect(Request $request): RedirectResponse
    {
        if (! $request->filled('return_page')) {
            return redirect()->route('receptionist.website.index', ['tab' => 'contents']);
        }

        $page = (string) $request->input('return_page');
        if (! array_key_exists($page, WebsiteContentRegistry::pages())) {
            $page = 'home';
        }

        return redirect()->route('receptionist.website.index', ['tab' => 'contents', 'page' => $page]);
    }
}
