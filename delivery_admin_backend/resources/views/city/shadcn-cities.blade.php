@extends('layouts.app')
@section('title', __('message.city'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? trans('message.city') }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @if($button ?? '')
                                    {!! $button !!}
                                @endif
                                <a href="{{ route('city.index', ['classic' => 1]) }}" class="shadcn-button shadcn-button-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ __('message.classic_view') }}
                                </a>
                            </div>
                        </div>

                        <!-- ShadCN Table Filters -->
                        <div class="p-3 border-bottom">
                            <form action="{{ route('refactored-city.index') }}" method="GET" class="shadcn-filters">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="country_id" class="form-label">{{ __('message.country') }}</label>
                                        <select name="country_id" id="country_id" class="form-control shadcn-select">
                                            <option value="">{{ __('message.all') }}</option>
                                            @foreach(\App\Models\Country::pluck('name', 'id') as $id => $name)
                                                <option value="{{ $id }}" {{ request('country_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">{{ __('message.status') }}</label>
                                        <select name="status" id="status" class="form-control shadcn-select">
                                            <option value="">{{ __('message.all') }}</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('message.active') }}</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('message.inactive') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="search" class="form-label">{{ __('message.search') }}</label>
                                        <input type="text" name="search" id="search" class="form-control shadcn-input" value="{{ request('search') }}" placeholder="{{ __('message.search_by_name') }}">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="shadcn-button shadcn-button-primary mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                                        {{ __('message.filter') }}
                                    </button>
                                    <a href="{{ route('refactored-city.index') }}" class="shadcn-button shadcn-button-outline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path><path d="M8 16H3v5"></path></svg>
                                        {{ __('message.reset') }}
                                    </a>
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
                                        <th>{{ __('message.name') }}</th>
                                        <th>{{ __('message.country') }}</th>
                                        <th>{{ __('message.status') }}</th>
                                        <th>{{ __('message.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($cities->count() > 0)
                                        @foreach($cities as $city)
                                            <tr>
                                                @if($multi_checkbox_delete)
                                                <td>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input select_checkbox" id="select_{{ $city->id }}" data-id="{{ $city->id }}">
                                                        <label class="custom-control-label" for="select_{{ $city->id }}"></label>
                                                    </div>
                                                </td>
                                                @endif
                                                <td>{{ $city->id }}</td>
                                                <td>{{ $city->name }}</td>
                                                <td>{{ $city->country ? $city->country->name : __('message.not_found') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $city->status ? 'success' : 'danger' }}">
                                                        {{ $city->status ? __('message.active') : __('message.inactive') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @if(auth()->user()->can('city-edit'))
                                                            <a href="{{ route('refactored-city.edit', $city->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-secondary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->can('city-delete'))
                                                            <form action="{{ route('refactored-city.destroy', $city->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="shadcn-button shadcn-button-sm shadcn-button-destructive" onclick="return confirm('{{ __('message.delete_form_message', ['form' => __('message.city')]) }}')">
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
                                            <td colspan="{{ $multi_checkbox_delete ? '6' : '5' }}" class="text-center">{{ __('message.no_data_found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $cities->appends(request()->query())->links() }}
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
                        checked_title: 'city-checked'
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
