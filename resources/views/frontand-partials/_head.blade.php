<link rel="icon" type="image/x-icon" href="{{ \App\Helpers\LogoHelper::getLogo('site_favicon') }}">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/toastr.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('vendor/intlTelInput/css/intlTelInput.css') }}">
@if(mighty_language_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
@endif