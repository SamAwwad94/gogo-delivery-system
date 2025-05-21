<x-frontand-layout :assets="$assets ?? []">

    <div class="container-fluid delivery_partner_main_section">
        <div class="row">
            <div class="col-lg-5 offset-lg-1 col-md-6 d-flex flex-column justify-content-center deliverypartner">
                <h1 class="fw-bold mb-2 delivery-partner-content">{{ $data['delivery_partner']['title'] }}</h1>
                <p class="mt-2 mb-2 deliverypartner-p">{{ $data['delivery_partner']['subtitle'] }}</p>
                <a href="{{ $data['delivery_partner']['play_store_link']['url'] }}" {{ $data['delivery_partner']['play_store_link']['target'] }} class="text-decoration-none text-white text-center delivery-partner-btn">{{ strtoupper(__('message.delivery_partner_message_list', ['name' =>  SettingData('app_content', 'app_name')])) }}</a>
            </div>

            <div class="col-lg-5 col-md-6">
                <div class="delivery_image_section d-flex justify-content-center align-item-center">
                    <img src="{{ getSingleMediaSettingImage(getSettingFirstData('delivery_partner','delivery_partner_image'),'delivery_partner_image') }}"class="img-fluid delivery-partner-img">
                </div>
            </div>
        </div>
    </div>

    <div class="container deliverysection">
        <div class="row">
            @foreach ($delivery_data as $item)
                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="d-flex justify-content-center">
                        @if ($item->getFirstMedia('frontend_data_image'))
                            <img src="{{ $item->getFirstMedia('frontend_data_image')->getUrl() }}" alt="Delivery Image" height="164px" width="164px">
                        @else
                            <div class="rounded-circle delivery-div"></div>
                        @endif
                    </div>
                    <div class="text-center delivery-second-section">
                        <h4>{{ $item->title }}</h4>
                        <p>{{ $item->subtitle }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-frontand-layout>
