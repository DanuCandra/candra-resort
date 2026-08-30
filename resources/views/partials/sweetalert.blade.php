<script src="{{ asset('dashboard/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[data-confirm]');
        if (!form || form.dataset.confirmed === 'true') return;
        event.preventDefault();
        Swal.fire({
            title: form.dataset.confirmTitle || 'Apakah Anda yakin?',
            text: form.dataset.confirm,
            icon: form.dataset.confirmIcon || 'warning',
            showCancelButton: true,
            confirmButtonText: form.dataset.confirmButton || 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#5d87ff'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.requestSubmit();
            }
        });
    });
</script>
