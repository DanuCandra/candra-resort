<script src="{{ asset('dashboard/assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme/app.init.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme/theme.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme/app.min.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/theme/sidebarmenu.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
@include('partials.notifications')
@include('partials.sweetalert')
@stack('scripts')
