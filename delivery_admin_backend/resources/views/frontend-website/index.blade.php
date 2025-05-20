<x-frontand-layout :assets="$assets ?? []">
    
    <!-- START FIRST SECTION -->
    <div class="container-fluid delivery-section">
        <div class="row first-main-class">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="text-content">

                    <h1 class="fw-normal mb-0">{{ $data['app_content']['app_name'] }}</h1>
                    <span class="hr"></span><span class="space"></span><span class="hr2"></span>
                    <p class="first-p mt-4">{{ $data['app_content']['create_order_description'] }}</p>
                    <div class="d-grid">
                        @if(Auth::check() && Auth::user()->user_type == 'client')
                            <a class="btn create-order-btn text-white mt-1 mb-2 p-2 w-50" href="{{ route('order.create')}}" role="button">{{ __('message.create_order') }}</a>
                        @elseif(Auth::check() && Auth::user()->user_type == 'admin')
                            <a class="btn create-order-btn text-white mt-1 mb-2 p-2 w-50" href="{{ route('order.create')}}" role="button">{{ __('message.create_order') }}</a>
                        @elseif(Auth::check() && Auth::user())
                            <a class="btn create-order-btn text-white mt-1 mb-2 p-2 w-50" href="{{ route('order.create')}}" role="button">{{ __('message.create_order') }}</a>
                        @else
                            <a class="btn create-order-btn text-white mt-1 mb-2 p-2 w-50" href=" {{ route('admin-login') }}"data-bs-toggle="modal"
                            data-bs-target="#signinModal" role="button">{{ __('message.create_order') }}</a>            
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row img-background">
            <div class="col-lg-6 col-md-6 col-sm-12 offset-lg-6 offset-md-3 bike-image">
                <img src="{{ $data['app_content']['delivery_man_image'] }}" class="img-fluid bike">
            </div>

            <div class="col-12 col-lg-12 col-md-12 co-sm-12 m-0 p-0 road-image">
                <img src="{{ $data['app_content']['delivery_road_image'] }}" class="img-fluid w-100">
            </div>
        </div>
    </div>
    <!-- END FIRST SECTION -->


    <!-- START SECOND SECTION -->
    <div class="container-fluid second-section px-lg-5">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-4">
                <h1 class="fw-normal mb-0">{{ $data['why_choose']['title'] }}</h1>
                <span class="hr"></span><span class="space"></span><span class="hr2"></span>
                <p class="section-p mt-4">{{ $data['why_choose']['description'] }}</p>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    @foreach ($why_delivery as $item)
                        <div class="col-6 col-md-4 col-lg-4 mb-4">
                            <div class="card">
                                <img src="{{ getSingleMediaSettingImage($item->id != null ? $item : null ,'frontend_data_image','why_delivery_image') }}" alt="mightydelivery" class="card-img-top why-section-img">

                                <div class="card-body">
                                    <h3 class="second-section-h3 mt-3">{{ $item->title }}</h3>
                                    <p class="card-text text-center second-section-p">{{ $item->subtitle }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-1"></div>
        </div>
    </div>
    <!-- END SECOND SECTION -->


    <!-- START TESTIMONIAL SECTION -->

    <div class="multiple-card-slider ">
        <div id="carouselExampleControls" class="carousel carousel-dark" data-bs-ride="false">
            <section class="home-testimonial">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-6 pt-4 d-flex flex-column align-items-center testimonial-center">
                            <h2 class="text-center mb-3">{{ $data['client_review']['client_review_title'] }}</h2>
                        </div>
                    </div>

                    <div class="container">
                        <div class="carousel-inner" id="onecard-flex">
                            @foreach ($client_review as $key => $item)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <div class="card11 card-box">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center pt-2 pb-2">
                                                <img src="{{ getSingleMediaSettingImage($item->id != null ? $item : null ,'frontend_data_image','client_review_image') }}" alt="mightydelivery" class="rounded-circle shadow-1-strong mb-4 client-review-card" height="150px" width="150px">
                                            </div>
                                            <div class="testimonial-title d-flex justify-content-center">
                                                {{ $item->title }}</div>
                                            <div class="testimonial-email d-flex justify-content-center mb-2">
                                                {{ $item->subtitle }}</div>
                                            <p class="card-text text-center testimonial-text"><i
                                                    class="fas fa-quote-left pe-2"></i>{{ $item->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('message.previous') }}</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">{{ __('message.next') }}</span>
            </button>
        </div>
    </div>

    <!-- END TESTIMONIAL SECTION -->

    <!-- START MOBILE SECTION -->
    <div class="container mobile-section">
        <div class="row">
            <div class="col-lg-6 d-flex justify-content-center">
                <img src="{{ $data['download_app']['download_app_logo'] }}" class="img-fluid mobile-img">
            </div>
            <div class="col-lg-6 my-auto mt-5 py-4">
                <h1 class="fw-normal">{{ $data['download_app']['download_title'] }}</h1>
                <span class="hr"></span><span class="space"></span><span class="hr2"></span>
                <p class="mobile-section-p mt-3">{{ $data['download_app']['download_description'] }}</p>
                <a href="{{ $data['download_app']['play_store_link']['url'] }}" {{ $data['download_app']['play_store_link']['target'] }} class="text-decoration-none">
                    <img src="{{ asset('frontend-website/assets/website/ic_play_store.png') }}" alt="Play Store" class="mt-3 me-2">
                </a>
                <a href="{{ $data['download_app']['app_store_link']['url'] }}" {{ $data['download_app']['app_store_link']['target'] }}>
                    <img src="{{ asset('frontend-website/assets/website/ic_app_store.png') }}" alt="App Store" class="mt-3 me-2">
                </a>
            </div>
        </div>
    </div>
    <!-- END MOBILE SECTION -->
</x-frontand-layout>
