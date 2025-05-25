<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle ?? 'Orders' }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('message.id') }}</th>
                                        <th>{{ __('message.customer') }}</th>
                                        <th>{{ __('message.delivery_man') }}</th>
                                        <th>{{ __('message.datetime') }}</th>
                                        <th>{{ __('message.status') }}</th>
                                        <th>{{ __('message.payment_status') }}</th>
                                        <th>{{ __('message.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($orders) && $orders->count() > 0)
                                        @foreach($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->client ? $order->client->name : '-' }}</td>
                                                <td>{{ $order->delivery_man ? $order->delivery_man->name : '-' }}</td>
                                                <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($order->payment_status ?? 'pending') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('order.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('message.no_record_found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if(isset($orders) && method_exists($orders, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
        <script>
            $(document).ready(function () {
                // Simple table display without DataTable for now
                console.log('Orders page loaded successfully');
            });
        </script>
    @endsection
</x-master-layout>