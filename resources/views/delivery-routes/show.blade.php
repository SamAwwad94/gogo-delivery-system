@extends('layouts.app')
@section('title') {{__('message.view_form_title', ['form' => __('message.delivery_route')])}} @endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="font-weight-bold">
                            {{ $pageTitle ?? __('message.view_form_title', ['form' => __('message.delivery_route')]) }}</h5>
                        <div>
                            <a href="{{ route('delivery-routes.map', $route->id) }}" class="btn btn-sm btn-info mr-1"><i
                                    class="fa fa-map-marked-alt"></i> {{ __('message.view_map') }}</a>
                            <a href="{{ route('delivery-routes.map', ['id' => $route->id, 'shadcn' => 'true']) }}"
                                class="btn btn-sm btn-secondary mr-1"><i class="fa fa-map-marked-alt"></i>
                                {{ __('message.view_map') }} (ShadCN)</a>
                            <a href="{{ route('delivery-routes.edit', $route->id) }}" class="btn btn-sm btn-primary mr-1"><i
                                    class="fa fa-pen"></i> {{ __('message.edit') }}</a>
                            <a href="{{ route('delivery-routes.index') }}" class="btn btn-sm btn-secondary"><i
                                    class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('message.route_details') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="150">{{ __('message.name') }}</th>
                                            <td>{{ $route->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('message.status') }}</th>
                                            <td>{!! $route->status_badge !!}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('message.delivery_man') }}</th>
                                            <td>
                                                @if($route->deliveryMan)
                                                    <a href="{{ route('deliveryman.show', $route->deliveryMan->id) }}">
                                                        {{ $route->deliveryMan->display_name }}
                                                    </a>
                                                @else
                                                    {{ __('message.not_found') }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('message.start_location') }}</th>
                                            <td>{{ $route->start_location }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('message.coordinates') }}</th>
                                            <td>{{ $route->start_latitude }}, {{ $route->start_longitude }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('message.created_at') }}</th>
                                            <td>{{ $route->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ __('message.change_status') }}</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('delivery-routes.status', $route->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="status">{{ __('message.status') }}</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="pending" {{ $route->status == 'pending' ? 'selected' : '' }}>
                                                    {{ __('message.pending') }}</option>
                                                <option value="in_progress" {{ $route->status == 'in_progress' ? 'selected' : '' }}>{{ __('message.in_progress') }}</option>
                                                <option value="completed" {{ $route->status == 'completed' ? 'selected' : '' }}>{{ __('message.completed') }}</option>
                                                <option value="cancelled" {{ $route->status == 'cancelled' ? 'selected' : '' }}>{{ __('message.cancelled') }}</option>
                                            </select>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('message.update_status') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ __('message.orders') }} ({{ $route->orders->count() }})</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('message.client') }}</th>
                                                    <th>{{ __('message.pickup_point') }}</th>
                                                    <th>{{ __('message.delivery_point') }}</th>
                                                    <th>{{ __('message.status') }}</th>
                                                    <th>{{ __('message.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($route->orders as $order)
                                                    <tr>
                                                        <td>{{ $order->id }}</td>
                                                        <td>{{ optional($order->client)->name ?? 'N/A' }}</td>
                                                        <td>{{ Str::limit($order->pickup_point, 20) }}</td>
                                                        <td>{{ Str::limit($order->delivery_point, 20) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ getStatusBadgeClass($order->status) }}">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('order.show', $order->id) }}"
                                                                class="btn btn-sm btn-info">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">{{ __('message.no_data_found') }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection