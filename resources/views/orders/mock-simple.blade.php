<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.order_list') }}</h4>
            <h6>{{ __('message.manage_your_orders') }}</h6>
        </div>
        <div class="page-btn">
            {!! $button !!}
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2>Mock Orders Page</h2>
            <p>This is a simple mock page to test if the route and controller are working correctly.</p>
            
            <div class="mt-4">
                <h3>Mock Orders:</h3>
                <ul>
                    @foreach($mockOrders as $order)
                        <li>{{ $order['id'] }} - {{ $order['customer'] }} - {{ $order['status_label'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-master-layout>
