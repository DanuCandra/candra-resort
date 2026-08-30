<footer class="footer-section" id="site-footer">
    <div class="container">
        <div class="footer-text">
            <div class="row">
                <div class="col-lg-4">
                    <div class="ft-about">
                        <div class="logo"><a href="{{ route('home') }}"><h3 class="text-white">{{ $siteSettings->get('hotel.name', 'Candra Resort') }}</h3></a></div>
                        <p>{{ $siteContents->get('footer_summary')?->content ?? 'Tempat beristirahat yang hangat, nyaman, dan berkesan untuk setiap perjalanan Anda.' }}</p>
                        <div class="fa-social">@if($siteSettings->get('social.facebook'))<a href="{{ $siteSettings->get('social.facebook') }}" target="_blank" rel="noopener"><i class="fa fa-facebook"></i></a>@endif @if($siteSettings->get('social.instagram'))<a href="{{ $siteSettings->get('social.instagram') }}" target="_blank" rel="noopener"><i class="fa fa-instagram"></i></a>@endif</div>
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <div class="ft-contact"><h6>Hubungi Kami</h6><ul><li>{{ $siteSettings->get('hotel.phone', '+62 812 3456 7890') }}</li><li>{{ $siteSettings->get('hotel.email', 'info@candraresort.test') }}</li><li>{{ $siteSettings->get('hotel.address', 'Indonesia') }}</li></ul></div>
                </div>
                <div class="col-lg-4">
                    <div class="ft-newslatter"><h6>{{ $siteContents->get('footer_reservation')?->title ?? 'Reservasi' }}</h6><p>{{ $siteContents->get('footer_reservation')?->content ?? 'Temukan kamar yang sesuai untuk perjalanan Anda.' }}</p><a href="{{ route('public.rooms.index') }}" class="primary-btn text-white">Lihat Kamar</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-option">
        <div class="container"><div class="row"><div class="col-lg-7"><ul><li><a href="{{ route('public.contact') }}">Kontak</a></li><li><a href="{{ route('public.about').'#hotel-policies' }}">Kebijakan Hotel</a></li></ul></div><div class="col-lg-5"><div class="co-text"><p>&copy; {{ now()->year }} {{ $siteSettings->get('hotel.name', 'Candra Resort') }}. All rights reserved.</p></div></div></div></div>
    </div>
</footer>
