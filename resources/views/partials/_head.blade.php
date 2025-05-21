<link rel="shortcut icon" class="site_favicon_preview" href="{{ \App\Helpers\LogoHelper::getLogo('site_favicon') }}" />
<link rel="stylesheet" href="{{ asset('css/backend-bundle.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/backend.css') }}" />
@if(mighty_language_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css') }}" />
<link rel="stylesheet" href="{{ asset('css/vendor/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('vendor/confirmJS/jquery-confirm.min.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/magnific-popup/css/magnific-popup.css') }}" />
<link rel="stylesheet" href="{{ asset('css/custom.css')}}">
@if(isset($assets) && in_array('phone', $assets))
    <link rel="stylesheet" href="{{ asset('vendor/intlTelInput/css/intlTelInput.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

<script>
    // Force refreshing logo previews on page load to prevent stale caches
    document.addEventListener('DOMContentLoaded', function () {
        // Add random query parameter to logo preview images
        const timestamp = new Date().getTime() + Math.floor(Math.random() * 1000);
        const logoElements = document.querySelectorAll('.site_logo_preview, .site_dark_logo_preview, .site_favicon_preview');

        logoElements.forEach(function (element) {
            if (element.src) {
                // Remove any existing query parameters
                let src = element.src.split('?')[0];
                // Add fresh timestamp
                element.src = src + '?v=' + timestamp;
            }
        });
    });
</script><link rel="stylesheet" href="{{ asset('css/logo-loader.css') }}">
