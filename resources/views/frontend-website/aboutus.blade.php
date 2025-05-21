<x-frontand-layout :assets="$assets ?? []">

    <div class="container-fluid about-section-content">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10 p-2 mt-1">
                <p class="mt-1">{{ $data['about_us']['long_des'] }}</p>
            </div>
            <div class="col-lg-1"></div>
        </div>
    </div>

    <div class="container-fluid about-section">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-5 col-md-6 my-auto mx-auto">
                <h1 class="fw-bold display-6">{{ $data['about_us']['download_title'] }}</h1>
                <p class="mt-4 mb-4">{{ $data['about_us']['download_subtitle'] }}</p>
                <a href="{{ $data['about_us']['play_store_link']['url'] }}" {{ $data['about_us']['play_store_link']['target'] }} class="text-decoration-none">
                    <img src="{{ asset('frontend-website/assets/website/ic_play_store.png') }}" alt="Play Store" class="mt-3 me-2">
                </a>
                <a href="{{ $data['about_us']['app_store_link']['url'] }}" {{ $data['about_us']['app_store_link']['target'] }}>
                    <img src="{{ asset('frontend-website/assets/website/ic_app_store.png') }}" alt="App Store" class="mt-3 me-2">
                </a>
            </div>
            <div class="col-lg-6 col-md-6 d-flex about-img-section">
                <img src="{{ getSingleMediaSettingImage(getSettingFirstData('about_us','about_us_app_ss'),'about_us_app_ss') }}" class="aboutus-img">
            </div>
        </div>
    </div>

</x-frontand-layout>
