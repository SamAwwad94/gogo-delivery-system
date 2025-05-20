<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.delivery_man_list') }}</h4>
            <h6>{{ __('message.manage_your_delivery_men') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('delivery-man.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_new_delivery_man') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="delivery-men-table"></div>
        </div>
    </div>
    
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <script src="{{ asset('js/shadcn-filter-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-table-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-filter-configs.js') }}"></script>
    <script src="{{ asset('js/enhanced-delivery-men-table.js') }}"></script>
</x-master-layout>
