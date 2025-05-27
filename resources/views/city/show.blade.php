<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="font-weight-bold text-uppercase">{{ optional($data)->name }}</h4>
                        </div>
                        <a href="{{ route('city.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-angle-double-left"></i> {{ __('message.back') }}
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.city_id'))->class('form-control-label text-secondary') }}
                                    <h6>{{ optional($data)->id }}</h6>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.min_distance') . ' (km)')->class('form-control-label text-secondary') }}
                                    <h4>{{ optional($data)->min_distance }}</h4>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.min_weight') . ' (kg)')->class('form-control-label text-secondary') }}
                                    <h4>{{ optional($data)->min_weight }}</h4>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.fixed_charges'))->class('form-control-label text-secondary') }}
                                    <h4>{{ getPriceFormat($data->fixed_charges) }}</h4>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.cancel_charges'))->class('form-control-label text-secondary') }}
                                    <h4>{{ getPriceFormat($data->cancel_charges) }}</h4>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.per_distance_charges'))->class('form-control-label text-secondary') }}
                                    <h4>{{ getPriceFormat($data->per_distance_charges) }}</h4>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.per_weight_charges'))->class('form-control-label text-secondary') }}
                                    <h4>{{ getPriceFormat($data->per_weight_charges) }}</h4>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.admin_commission'))->class('form-control-label text-secondary') }}
                                    <h4>
                                        {{ $data->commission_type === 'percentage' ? $data->admin_commission . ' %' : $data->admin_commission }}
                                    </h4>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.created_at'))->class('form-control-label text-secondary') }}
                                    <h6>{{ dateAgoFormate($data->created_at) }}</h6>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.updated_at'))->class('form-control-label text-secondary') }}
                                    <h6>{{ dateAgoFormate($data->updated_at) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>
</x-master-layout>