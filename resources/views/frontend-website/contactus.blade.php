<x-frontand-layout :assets="$assets ?? []">
    
    <div class="container-fluid contactus-section">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-7 col-md-6 d-flex justify-content-center flex-column">
                <h1 class="fw-bold display-5 contactus-title">{{ $data['contact_us']['contact_title'] }}</h1>
                <p class="mt-4 mb-0 contactus-description">{{ $data['contact_us']['contact_subtitle'] }}</p>
                <h3 class="mt-0 fw-bold mb-0 contactus-visit">{{ __('message.visit_us') }}</h3>
                <hr class="full-width-hr mt-2">
                <h3 class="mb-3 fw-bold text-uppercase">{{ SettingData('app_content', 'app_name') ?? $data['dummy_title'] }}</h3>
                <div>
                    <a class="text-muted text-decoration-none"
                        href="{{ $data['app_setting_data']->support_email ?? 'javascript:void(0)' }}"
                        {{ $data['app_setting_data']->support_email != null ? 'target="_blank"' : '' }}>
                        <i class="fa-solid fa-circle-question fa-sm me-2"></i>
                        {{ $data['app_setting_data']->support_email ?? $data['dummy_title'] }}
                    </a>
                </div>
                <div class="mb-4">
                    <a href="{{ $data['app_setting_data']->site_email ? 'mailto:' . $data['app_setting_data']->site_email : 'javascript:void(0)' }}"
                        {{ $data['app_setting_data']->site_email ? 'target="_blank"' : '' }}
                        class="text-muted text-decoration-none">
                        <i class="fa-solid fa-envelope fa-sm me-2"></i>
                        {{ $data['app_setting_data']->site_email ?? $data['dummy_title'] }}
                    </a>
                </div>
                <div>
                    <a href="{{ $data['contact_us']['play_store_link']['url'] }}" {{ $data['contact_us']['play_store_link']['target'] }} class="text-decoration-none">
                        <img src="{{ asset('frontend-website/assets/website/ic_play_store.png') }}" alt="Play Store" class="mt-3 me-2">
                    </a>
                    <a href="{{ $data['contact_us']['app_store_link']['url'] }}" {{ $data['contact_us']['app_store_link']['target'] }}>
                        <img src="{{ asset('frontend-website/assets/website/ic_app_store.png') }}" alt="App Store" class="mt-3 me-2">
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                <img src="{{ getSingleMediaSettingImage(getSettingFirstData('contact_us','contact_us_app_ss'),'contact_us_app_ss') }}" class="img-fluid contactus-img">
            </div>
        </div>
    </div>

</x-frontand-layout>
