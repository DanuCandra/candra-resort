<footer class="footer-section">
    <div class="container">
        <div class="footer-text">
            <div class="row">
                <div class="col-lg-4">
                    <div class="ft-about">
                        <div class="logo"><a href="{{ route('home') }}"><h3 class="text-white">Candra Resort</h3></a></div>
                        <p>Tempat beristirahat yang hangat, nyaman, dan berkesan untuk setiap perjalanan Anda.</p>
                        <div class="fa-social"><a href="#"><i class="fa fa-facebook"></i></a><a href="#"><i class="fa fa-instagram"></i></a></div>
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <div class="ft-contact"><h6>Hubungi Kami</h6><ul><li>+62 812 3456 7890</li><li>info@candraresort.test</li><li>Indonesia</li></ul></div>
                </div>
                <div class="col-lg-4">
                    <div class="ft-newslatter"><h6>Reservasi</h6><p>Temukan kamar yang sesuai untuk perjalanan Anda.</p><a href="{{ route('public.rooms.index') }}" class="primary-btn text-white">Lihat Kamar</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-option">
        <div class="container"><div class="row"><div class="col-lg-7"><ul><li><a href="{{ route('public.contact') }}">Kontak</a></li><li><a href="#">Kebijakan Privasi</a></li></ul></div><div class="col-lg-5"><div class="co-text"><p>&copy; {{ now()->year }} Candra Resort. All rights reserved.</p></div></div></div></div>
    </div>
</footer>
