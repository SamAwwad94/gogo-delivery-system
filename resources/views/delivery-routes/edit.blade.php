@extends('layouts.app')
@section('title') {{__('message.update_form_title',['form' => __('message.delivery_route')])}} @endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.update_form_title',['form' => __('message.delivery_route')]) }}</h5>
                    <a href="{{ route('delivery-routes.index') }}" class="float-right btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                </div>
                
                <form action="{{ route('delivery-routes.update', $route->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="name" class="form-control-label">{{ __('message.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $route->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="delivery_man_id" class="form-control-label">{{ __('message.delivery_man') }} <span class="text-danger">*</span></label>
                                <select name="delivery_man_id" id="delivery_man_id" class="form-control select2 @error('delivery_man_id') is-invalid @enderror" required>
                                    <option value="">{{ __('message.select_name',['select' => __('message.delivery_man')]) }}</option>
                                    @foreach($deliveryMen as $deliveryMan)
                                        <option value="{{ $deliveryMan->id }}" {{ old('delivery_man_id', $route->delivery_man_id) == $deliveryMan->id ? 'selected' : '' }}>{{ $deliveryMan->display_name }}</option>
                                    @endforeach
                                </select>
                                @error('delivery_man_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="start_location" class="form-control-label">{{ __('message.start_location') }} <span class="text-danger">*</span></label>
                                <input type="text" name="start_location" id="start_location" class="form-control @error('start_location') is-invalid @enderror" value="{{ old('start_location', $route->start_location) }}" required>
                                @error('start_location')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="start_latitude" class="form-control-label">{{ __('message.latitude') }} <span class="text-danger">*</span></label>
                                <input type="text" name="start_latitude" id="start_latitude" class="form-control @error('start_latitude') is-invalid @enderror" value="{{ old('start_latitude', $route->start_latitude) }}" required>
                                @error('start_latitude')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="start_longitude" class="form-control-label">{{ __('message.longitude') }} <span class="text-danger">*</span></label>
                                <input type="text" name="start_longitude" id="start_longitude" class="form-control @error('start_longitude') is-invalid @enderror" value="{{ old('start_longitude', $route->start_longitude) }}" required>
                                @error('start_longitude')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div id="map" style="height: 400px;"></div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="orders" class="form-control-label">{{ __('message.orders') }} <span class="text-danger">*</span></label>
                                <select name="orders[]" id="orders" class="form-control select2 @error('orders') is-invalid @enderror" multiple required>
                                    @foreach($pendingOrders as $order)
                                        <option value="{{ $order->id }}" {{ (old('orders') && in_array($order->id, old('orders'))) || (in_array($order->id, $route->orders->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            #{{ $order->id }} - {{ optional($order->client)->name ?? 'N/A' }} - {{ $order->pickup_point }} to {{ $order->delivery_point }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('orders')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('message.update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('bottom_script')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
<script>
    let map;
    let marker;
    
    function initMap() {
        // Get coordinates from form
        const lat = parseFloat(document.getElementById('start_latitude').value) || 33.8938;
        const lng = parseFloat(document.getElementById('start_longitude').value) || 35.5018;
        
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat, lng },
            zoom: 13,
        });
        
        // Create marker
        marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            draggable: true,
            title: "{{ __('message.start_location') }}"
        });
        
        // Update coordinates when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function() {
            const position = marker.getPosition();
            document.getElementById('start_latitude').value = position.lat();
            document.getElementById('start_longitude').value = position.lng();
            
            // Get address from coordinates
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: position }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    document.getElementById('start_location').value = results[0].formatted_address;
                }
            });
        });
        
        // Create search box
        const input = document.getElementById('start_location');
        const searchBox = new google.maps.places.SearchBox(input);
        
        // Listen for the event fired when the user selects a prediction
        searchBox.addListener('places_changed', function() {
            const places = searchBox.getPlaces();
            
            if (places.length === 0) {
                return;
            }
            
            const place = places[0];
            
            // Update marker position
            marker.setPosition(place.geometry.location);
            map.setCenter(place.geometry.location);
            
            // Update form fields
            document.getElementById('start_latitude').value = place.geometry.location.lat();
            document.getElementById('start_longitude').value = place.geometry.location.lng();
        });
    }
    
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
