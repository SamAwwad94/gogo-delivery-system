<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.add_delivery_route') }}</h4>
            <h6>{{ __('message.create_new_delivery_route') }}</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('delivery-routes.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('message.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('message.deliveryman') }} <span class="text-danger">*</span></label>
                            <select name="deliveryman_id" class="select" required>
                                <option value="">{{ __('message.select_deliveryman') }}</option>
                                @foreach($deliverymen as $deliveryman)
                                    <option value="{{ $deliveryman->id }}" {{ old('deliveryman_id') == $deliveryman->id ? 'selected' : '' }}>
                                        {{ $deliveryman->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('deliveryman_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('message.start_location') }} <span class="text-danger">*</span></label>
                            <input type="text" name="start_location" value="{{ old('start_location') }}" required>
                            @error('start_location')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('message.end_location') }} <span class="text-danger">*</span></label>
                            <input type="text" name="end_location" value="{{ old('end_location') }}" required>
                            @error('end_location')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>{{ __('message.waypoints') }}</label>
                            <textarea name="waypoints" class="form-control">{{ old('waypoints') }}</textarea>
                            <small class="text-muted">{{ __('message.waypoints_help') }}</small>
                            @error('waypoints')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>{{ __('message.description') }}</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label>{{ __('message.status') }}</label>
                            <select name="status" class="select">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                    {{ __('message.active') }}
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    {{ __('message.inactive') }}
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-submit me-2">{{ __('message.submit_form') }}</button>
                            <a href="{{ route('delivery-routes.index') }}"
                                class="btn btn-cancel">{{ __('message.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </x-master-layout>