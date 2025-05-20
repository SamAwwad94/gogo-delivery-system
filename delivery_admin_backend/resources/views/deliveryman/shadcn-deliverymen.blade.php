@extends('layouts.app')
@section('title', __('message.delivery_man'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? trans('message.delivery_man') }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @if($button ?? '')
                                    {!! $button !!}
                                @endif
                                <a href="{{ route('deliveryman.index', ['classic' => 1]) }}" class="shadcn-button shadcn-button-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ __('message.classic_view') }}
                                </a>
                            </div>
                        </div>

                        <!-- ShadCN Table Filters -->
                        <div class="p-3 border-bottom">
                            <form action="{{ route('refactored-deliveryman.index') }}" method="GET" class="shadcn-filters">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label">{{ __('message.status') }}</label>
                                        <select name="status" id="status" class="form-control shadcn-select">
                                            <option value="">{{ __('message.all') }}</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('message.active') }}</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('message.inactive') }}</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('message.pending') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="country_id" class="form-label">{{ __('message.country') }}</label>
                                        <select name="country_id" id="country_id" class="form-control shadcn-select">
                                            <option value="">{{ __('message.select_name', ['select' => __('message.country')]) }}</option>
                                            @foreach($country as $id => $name)
                                                <option value="{{ $id }}" {{ $selectedCountryId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="city_id" class="form-label">{{ __('message.city') }}</label>
                                        <select name="city_id" id="city_id" class="form-control shadcn-select">
                                            <option value="">{{ __('message.select_name', ['select' => __('message.city')]) }}</option>
                                            @foreach($cities as $id => $name)
                                                <option value="{{ $id }}" {{ $selectedCityId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">{{ __('message.search') }}</label>
                                        <input type="text" name="search" id="search" class="form-control shadcn-input" value="{{ request('search') }}" placeholder="{{ __('message.search') }}">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="shadcn-button shadcn-button-primary mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                                        {{ __('message.filter') }}
                                    </button>
                                    {!! $reset_file_button !!}
                                </div>
                            </form>
                        </div>

                        <!-- ShadCN Table -->
                        <div class="table-responsive">
                            <table class="table shadcn-table">
                                <thead>
                                    <tr>
                                        @if($multi_checkbox_delete)
                                        <th width="50px">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="select_all">
                                                <label class="custom-control-label" for="select_all"></label>
                                            </div>
                                        </th>
                                        @endif
                                        <th>{{ __('message.id') }}</th>
                                        <th>{{ __('message.user') }}</th>
                                        <th>{{ __('message.contact_number') }}</th>
                                        <th>{{ __('message.address') }}</th>
                                        <th>{{ __('message.status') }}</th>
                                        <th>{{ __('message.is_verified') }}</th>
                                        <th>{{ __('message.is_online') }}</th>
                                        <th>{{ __('message.is_available') }}</th>
                                        <th>{{ __('message.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($deliveryMen->count() > 0)
                                        @foreach($deliveryMen as $deliveryMan)
                                            <tr>
                                                @if($multi_checkbox_delete)
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input select_checkbox" id="select_{{ $deliveryMan->id }}" data-id="{{ $deliveryMan->id }}">
                                                        <label class="custom-control-label" for="select_{{ $deliveryMan->id }}"></label>
                                                    </div>
                                                </td>
                                                @endif
                                                <td>{{ $deliveryMan->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ getSingleMedia($deliveryMan, 'profile_image', null) }}" alt="profile" class="avatar-40 rounded-circle">
                                                        <div class="ml-2">
                                                            <h6 class="mb-0">{{ $deliveryMan->display_name }}</h6>
                                                            <p class="mb-0">{{ $deliveryMan->email }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $deliveryMan->contact_number }}</td>
                                                <td>
                                                    {{ $deliveryMan->address }}
                                                    @if($deliveryMan->city)
                                                        <br>{{ $deliveryMan->city->name }}
                                                    @endif
                                                    @if($deliveryMan->country)
                                                        <br>{{ $deliveryMan->country->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $deliveryMan->status ? 'success' : 'danger' }}">
                                                        {{ $deliveryMan->status ? __('message.active') : __('message.inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $deliveryMan->is_verified_delivery_man ? 'success' : 'warning' }}">
                                                        {{ $deliveryMan->is_verified_delivery_man ? __('message.verified') : __('message.unverified') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $deliveryMan->is_online ? 'success' : 'secondary' }}">
                                                        {{ $deliveryMan->is_online ? __('message.online') : __('message.offline') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $deliveryMan->is_available ? 'success' : 'warning' }}">
                                                        {{ $deliveryMan->is_available ? __('message.available') : __('message.unavailable') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @if(auth()->user()->can('deliveryman-show'))
                                                            <a href="{{ route('refactored-deliveryman.show', $deliveryMan->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-primary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->can('deliveryman-edit'))
                                                            <a href="{{ route('refactored-deliveryman.edit', $deliveryMan->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-secondary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->can('deliveryman-delete'))
                                                            <form action="{{ route('refactored-deliveryman.destroy', $deliveryMan->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="shadcn-button shadcn-button-sm shadcn-button-destructive" onclick="return confirm('{{ __('message.delete_form_message', ['form' => __('message.delivery_man')]) }}')">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ $multi_checkbox_delete ? '10' : '9' }}" class="text-center">{{ __('message.no_data_found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $deliveryMen->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
<script>
    $(document).ready(function() {
        // Handle select all checkbox
        $('#select_all').on('change', function() {
            $('.select_checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Handle delete selected button
        $('#deleteSelectedBtn').on('click', function() {
            var selectedIds = [];
            $('.select_checkbox:checked').each(function() {
                selectedIds.push($(this).data('id'));
            });
            
            if (selectedIds.length === 0) {
                alert("{{ __('message.please_select_at_least_one_record') }}");
                return;
            }
            
            if (confirm("{{ __('message.delete_selected_confirm') }}")) {
                $.ajax({
                    url: "{{ route('datatble.destroySelected') }}",
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds,
                        checked_title: 'deliveryman-checked'
                    },
                    success: function(response) {
                        alert(response.message);
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert("{{ __('message.something_went_wrong') }}");
                    }
                });
            }
        });
    });
</script>
<style>
    .shadcn-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .shadcn-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-align: left;
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .shadcn-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .shadcn-table tr:hover {
        background-color: #f9fafb;
    }
    
    .shadcn-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .shadcn-button-sm {
        padding: 0.25rem 0.5rem;
    }
    
    .shadcn-button-primary {
        background-color: #2563eb;
        color: white;
        border: 1px solid #2563eb;
    }
    
    .shadcn-button-primary:hover {
        background-color: #1d4ed8;
    }
    
    .shadcn-button-secondary {
        background-color: #6b7280;
        color: white;
        border: 1px solid #6b7280;
    }
    
    .shadcn-button-secondary:hover {
        background-color: #4b5563;
    }
    
    .shadcn-button-destructive {
        background-color: #ef4444;
        color: white;
        border: 1px solid #ef4444;
    }
    
    .shadcn-button-destructive:hover {
        background-color: #dc2626;
    }
    
    .shadcn-button-outline {
        background-color: transparent;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    
    .shadcn-button-outline:hover {
        background-color: #f9fafb;
    }
    
    .shadcn-filters {
        background-color: #f9fafb;
        border-radius: 0.375rem;
        padding: 1rem;
    }
    
    .shadcn-input, .shadcn-select {
        width: 100%;
        padding: 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
    }
    
    .shadcn-input:focus, .shadcn-select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 1px #2563eb;
    }
</style>
@endsection
