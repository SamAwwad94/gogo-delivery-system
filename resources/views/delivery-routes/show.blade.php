<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.view_delivery_route') }}</h4>
            <h6>{{ __('message.delivery_route_details') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('delivery-routes.edit', $delivery_route->id) }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/edit.svg') }}" alt="img" class="me-1">
                {{ __('message.edit') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>{{ __('message.name') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ $delivery_route->name }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>{{ __('message.deliveryman') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ optional($delivery_route->deliveryman)->name }}"
                                class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>{{ __('message.start_location') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ $delivery_route->start_location }}" class="form-control"
                                readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>{{ __('message.end_location') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ $delivery_route->end_location }}" class="form-control"
                                readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>{{ __('message.waypoints') }}</label>
                        <textarea class="form-control" readonly>{{ $delivery_route->waypoints }}</textarea>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>{{ __('message.description') }}</label>
                        <textarea class="form-control" readonly>{{ $delivery_route->description }}</textarea>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>{{ __('message.status') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ $delivery_route->status }}" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>{{ __('message.created_at') }}</label>
                        <div class="input-group">
                            <input type="text" value="{{ $delivery_route->created_at->format('Y-m-d H:i:s') }}"
                                class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <a href="{{ route('delivery-routes.map', $delivery_route->id) }}" class="btn btn-primary me-2">
                            <i class="fa fa-map-marker"></i> {{ __('message.view_on_map') }}
                        </a>
                        <a href="{{ route('delivery-routes.map', ['id' => $delivery_route->id, 'regular' => 'true']) }}"
                            class="btn btn-secondary me-2">
                            <i class="fa fa-map-marker"></i> {{ __('message.view_on_map') }} (Regular)
                        </a>
                        <a href="{{ route('delivery-routes.index') }}"
                            class="btn btn-cancel">{{ __('message.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>