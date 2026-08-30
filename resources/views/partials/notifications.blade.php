<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notyf = new Notyf({ duration: 4200, dismissible: true, position: { x: 'right', y: 'top' } });
        const success = {{ Illuminate\Support\Js::from(session('success')) }};
        const error = {{ Illuminate\Support\Js::from(session('error')) }};
        const validationError = {{ Illuminate\Support\Js::from($errors->first()) }};
        if (success) notyf.success(success);
        if (error) notyf.error(error);
        if (validationError) notyf.error(validationError);
        window.appNotyf = notyf;
    });
</script>
