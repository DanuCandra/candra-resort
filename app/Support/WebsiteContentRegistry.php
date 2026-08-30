<?php

namespace App\Support;

final class WebsiteContentRegistry
{
    /** @return array<string, array{label: string, description: string, route: string, icon: string, sections: array<int, string>}> */
    public static function pages(): array
    {
        return [
            'home' => ['label' => 'Beranda', 'description' => 'Hero dan ringkasan utama landing page.', 'route' => 'home', 'icon' => 'ti-home', 'sections' => ['hero', 'home']],
            'about' => ['label' => 'Tentang', 'description' => 'Cerita, nilai, video, dan kebijakan hotel.', 'route' => 'public.about', 'icon' => 'ti-info-circle', 'sections' => ['about', 'policy']],
            'rooms' => ['label' => 'Kamar', 'description' => 'Banner halaman daftar kamar.', 'route' => 'public.rooms.index', 'icon' => 'ti-bed', 'sections' => ['rooms']],
            'facilities' => ['label' => 'Fasilitas', 'description' => 'Banner dan pengantar halaman fasilitas.', 'route' => 'public.facilities', 'icon' => 'ti-building-community', 'sections' => ['facilities']],
            'promotions' => ['label' => 'Promosi', 'description' => 'Banner halaman penawaran hotel.', 'route' => 'public.promotions.index', 'icon' => 'ti-discount-2', 'sections' => ['promotions']],
            'gallery' => ['label' => 'Galeri', 'description' => 'Banner halaman galeri publik.', 'route' => 'public.gallery', 'icon' => 'ti-photo', 'sections' => ['gallery']],
            'contact' => ['label' => 'Kontak', 'description' => 'Banner, pengantar, dan informasi reservasi.', 'route' => 'public.contact', 'icon' => 'ti-address-book', 'sections' => ['contact']],
            'footer' => ['label' => 'Footer', 'description' => 'Teks yang tampil di bagian bawah semua halaman.', 'route' => 'home', 'icon' => 'ti-layout-bottombar', 'sections' => ['footer']],
        ];
    }

    /** @return array<string, array<string, mixed>> */
    public static function slots(): array
    {
        return [
            'hero_title' => self::slot('home', 'hero', 'Judul Hero Beranda', 'Judul besar pertama yang dilihat pengunjung.', 'title', null, true, 'A Warm Escape at Candra Resort', null, '#home-hero', 10),
            'hero_description' => self::slot('home', 'hero', 'Deskripsi Hero Beranda', 'Kalimat pendukung di bawah judul hero.', null, 'Deskripsi hero', false, null, 'Nikmati ketenangan, layanan yang hangat, dan pengalaman menginap yang dirancang untuk membuat setiap perjalanan lebih berkesan.', '#home-hero', 20),
            'about_summary' => self::slot('home', 'home', 'Ringkasan Tentang di Beranda', 'Judul dan paragraf singkat sebelum tombol Selengkapnya.', 'Judul bagian', 'Ringkasan', true, 'Hospitality yang Hangat di Setiap Kunjungan', 'Candra Resort menghadirkan suasana nyaman untuk liburan keluarga, perjalanan bisnis, maupun waktu tenang bersama orang terdekat.', '#home-about', 30),
            'facilities_intro' => self::slot('home', 'home', 'Pengantar Fasilitas Beranda', 'Judul bagian fasilitas pada landing page.', 'Judul bagian', 'Teks kecil', false, 'Fasilitas Candra Resort', 'Yang Kami Sediakan', '#facilities', 40),
            'rooms_intro' => self::slot('home', 'home', 'Pengantar Kamar Beranda', 'Judul sebelum daftar tipe kamar unggulan.', 'Judul bagian', 'Teks kecil', false, 'Kamar Pilihan Kami', 'Temukan ruang terbaik untuk setiap perjalanan.', '#rooms', 50),
            'promotions_intro' => self::slot('home', 'home', 'Pengantar Promosi Beranda', 'Judul sebelum kartu promosi terbaru.', 'Judul bagian', 'Teks kecil', false, 'Promosi Terbaru', 'Penawaran Terbaik', '#home-promotions', 60),

            'about_hero' => self::slot('about', 'about', 'Banner Halaman Tentang', 'Judul, deskripsi, dan gambar banner halaman Tentang.', 'Judul banner', 'Deskripsi banner', true, 'Tentang Candra Resort', 'Keramahtamahan yang terasa seperti rumah.', '#page-top', 10),
            'about_story' => self::slot('about', 'about', 'Cerita Utama Hotel', 'Cerita yang tampil di bagian pembuka halaman Tentang.', 'Judul cerita', 'Isi cerita', true, 'Selamat Datang di Candra Resort', 'Kami menghadirkan tempat istirahat yang tenang dengan pelayanan personal, fasilitas lengkap, dan pengalaman menginap yang berkesan.', '#about-story', 20),
            'about_values' => self::slot('about', 'about', 'Nilai dan Keunggulan', 'Tuliskan satu keunggulan per baris untuk daftar centang.', 'Judul bagian', 'Daftar keunggulan', false, 'Mengapa Memilih Kami', "Kamar nyaman dan terawat\nLayanan tamu selama menginap\nFood & Beverage\nProses reservasi yang mudah", '#about-story', 30),
            'about_video' => self::slot('about', 'about', 'Banner Pengalaman', 'Teks dan gambar lebar di bagian bawah halaman Tentang.', 'Judul banner', 'Deskripsi banner', true, 'Temukan Hotel & Layanan Kami', 'Temukan pengalaman baru bersama Candra Resort.', '#about-experience', 40),
            'check_in_policy' => self::slot('about', 'policy', 'Kebijakan Check-in', 'Informasi waktu dan persyaratan check-in.', 'Judul kebijakan', 'Isi kebijakan', false, 'Waktu Check-in', 'Check-in mulai pukul 14.00 WIB dengan menunjukkan identitas yang masih berlaku.', '#hotel-policies', 50),
            'check_out_policy' => self::slot('about', 'policy', 'Kebijakan Check-out', 'Informasi batas waktu check-out.', 'Judul kebijakan', 'Isi kebijakan', false, 'Waktu Check-out', 'Check-out maksimal pukul 12.00 WIB.', '#hotel-policies', 60),

            'rooms_hero' => self::slot('rooms', 'rooms', 'Banner Halaman Kamar', 'Judul, deskripsi, dan gambar banner daftar kamar.', 'Judul banner', 'Deskripsi banner', true, 'Kamar & Suite', 'Temukan ruang terbaik untuk perjalanan Anda.', '#page-top', 10),
            'facilities_hero' => self::slot('facilities', 'facilities', 'Banner Halaman Fasilitas', 'Judul, deskripsi, dan gambar banner fasilitas.', 'Judul banner', 'Deskripsi banner', true, 'Fasilitas Hotel', 'Semua yang Anda perlukan untuk tinggal dengan nyaman.', '#page-top', 10),
            'promotions_hero' => self::slot('promotions', 'promotions', 'Banner Halaman Promosi', 'Judul, deskripsi, dan gambar banner promosi.', 'Judul banner', 'Deskripsi banner', true, 'Promosi', 'Lebih banyak pengalaman dengan penawaran terbaik.', '#page-top', 10),
            'gallery_hero' => self::slot('gallery', 'gallery', 'Banner Halaman Galeri', 'Judul, deskripsi, dan gambar banner galeri.', 'Judul banner', 'Deskripsi banner', true, 'Galeri Candra Resort', 'Lihat suasana yang menanti kunjungan Anda.', '#page-top', 10),
            'contact_hero' => self::slot('contact', 'contact', 'Banner Halaman Kontak', 'Judul, deskripsi, dan gambar banner kontak.', 'Judul banner', 'Deskripsi banner', true, 'Hubungi Kami', 'Kami siap membantu merencanakan masa inap Anda.', '#page-top', 10),
            'contact_intro' => self::slot('contact', 'contact', 'Pengantar Kontak', 'Judul dan paragraf di atas alamat serta nomor telepon.', 'Judul bagian', 'Isi pengantar', false, 'Candra Resort', 'Hubungi tim kami untuk informasi kamar, fasilitas, atau kebutuhan khusus.', '#contact-information', 20),
            'contact_reservation' => self::slot('contact', 'contact', 'Kotak Informasi Reservasi', 'Pesan bantuan reservasi di sisi kanan halaman Kontak.', 'Judul kotak', 'Isi pesan', false, 'Informasi Reservasi', 'Pertanyaan dapat disampaikan melalui telepon atau email. Tim kami siap membantu kebutuhan masa inap Anda.', '#contact-information', 30),

            'footer_summary' => self::slot('footer', 'footer', 'Deskripsi Footer', 'Deskripsi singkat hotel pada footer semua halaman.', null, 'Deskripsi footer', false, null, 'Tempat beristirahat yang hangat, nyaman, dan berkesan untuk setiap perjalanan Anda.', '#site-footer', 10),
            'footer_reservation' => self::slot('footer', 'footer', 'Ajakan Reservasi Footer', 'Judul dan teks di atas tombol Lihat Kamar.', 'Judul', 'Deskripsi', false, 'Reservasi', 'Temukan kamar yang sesuai untuk perjalanan Anda.', '#site-footer', 20),
        ];
    }

    /** @return array<string, mixed> */
    private static function slot(
        string $page,
        string $section,
        string $label,
        string $description,
        ?string $titleLabel,
        ?string $contentLabel,
        bool $image,
        ?string $defaultTitle,
        ?string $defaultContent,
        string $anchor,
        int $sortOrder,
    ): array {
        return compact('page', 'section', 'label', 'description', 'titleLabel', 'contentLabel', 'image', 'defaultTitle', 'defaultContent', 'anchor', 'sortOrder');
    }
}
