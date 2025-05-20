<x-master-layout>
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.user_list') }}</h4>
            <h6>{{ __('message.manage_your_users') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('user.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_new_user') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="users-table"></div>
        </div>
    </div>
    
    <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    <script src="{{ asset('js/shadcn-filter-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-table-component.js') }}"></script>
    <script src="{{ asset('js/shadcn-filter-configs.js') }}"></script>
    <script src="{{ asset('js/enhanced-users-table.js') }}"></script>
</x-master-layout>
