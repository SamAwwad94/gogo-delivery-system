@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div class="flex flex-row mb-1 sm:mb-0 justify-between w-full">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $pageTitle }}
                </h2>
                <div class="text-end">
                    <div class="flex flex-row space-x-2">
                        <div class="flex items-center">
                            <form action="{{ route('refactored-vehicle.index') }}" method="GET" class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('message.search') }}" class="shadcn-input h-9 w-[250px]">
                                </div>
                                <div>
                                    <button type="submit" class="shadcn-button shadcn-button-outline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
                                        {{ __('message.filter') }}
                                    </button>
                                    {!! $reset_file_button !!}
                                </div>
                            </form>
                        </div>
                        <div class="flex items-center">
                            {!! $button !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-4">
                <div class="max-w-full overflow-x-auto rounded-lg">
                    <div class="shadcn-table-container">
                        <table class="shadcn-table">
                            <thead class="shadcn-table-thead">
                                <tr class="shadcn-table-tr">
                                    @if($multi_checkbox_delete)
                                    <th class="shadcn-table-th w-[50px]">
                                        <input type="checkbox" id="selectAll" class="shadcn-checkbox">
                                    </th>
                                    @endif
                                    <th class="shadcn-table-th">{{ __('message.image') }}</th>
                                    <th class="shadcn-table-th">{{ __('message.title') }}</th>
                                    <th class="shadcn-table-th">{{ __('message.type') }}</th>
                                    <th class="shadcn-table-th">{{ __('message.capacity') }}</th>
                                    <th class="shadcn-table-th">{{ __('message.size') }}</th>
                                    <th class="shadcn-table-th">{{ __('message.status') }}</th>
                                    <th class="shadcn-table-th text-right">{{ __('message.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="shadcn-table-tbody">
                                @if(count($vehicles) > 0)
                                    @foreach($vehicles as $vehicle)
                                        <tr class="shadcn-table-tr">
                                            @if($multi_checkbox_delete)
                                            <td class="shadcn-table-td">
                                                <input type="checkbox" name="vehicle_checkbox" value="{{ $vehicle->id }}" class="shadcn-checkbox">
                                            </td>
                                            @endif
                                            <td class="shadcn-table-td">
                                                <img src="{{ getSingleMedia($vehicle, 'vehicle_image', null) }}" alt="{{ $vehicle->title }}" class="w-10 h-10 rounded-full">
                                            </td>
                                            <td class="shadcn-table-td font-medium">{{ $vehicle->title }}</td>
                                            <td class="shadcn-table-td">{{ $vehicle->type }}</td>
                                            <td class="shadcn-table-td">{{ $vehicle->capacity }}</td>
                                            <td class="shadcn-table-td">{{ $vehicle->size }}</td>
                                            <td class="shadcn-table-td">
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vehicle->status ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                    {{ $vehicle->status ? __('message.active') : __('message.inactive') }}
                                                </div>
                                            </td>
                                            <td class="shadcn-table-td text-right">
                                                <div class="relative inline-block text-left dropdown">
                                                    <button type="button" class="shadcn-button shadcn-button-ghost shadcn-button-sm p-0 w-8 h-8 rounded-full">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                                    </button>
                                                    <div class="shadcn-dropdown-menu">
                                                        @if($auth_user->can('vehicle-edit'))
                                                        <a href="{{ route('refactored-vehicle.edit', $vehicle->id) }}" class="shadcn-dropdown-item">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path><path d="m15 5 4 4"></path></svg>
                                                            {{ __('message.edit') }}
                                                        </a>
                                                        @endif
                                                        @if($auth_user->can('vehicle-show'))
                                                        <a href="{{ route('refactored-vehicle.show', $vehicle->id) }}" class="shadcn-dropdown-item">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                            {{ __('message.view') }}
                                                        </a>
                                                        @endif
                                                        @if($auth_user->can('vehicle-delete'))
                                                        <form action="{{ route('refactored-vehicle.destroy', $vehicle->id) }}" method="POST" onsubmit="return confirm('{{ __('message.delete_msg') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="shadcn-dropdown-item text-red-600 dark:text-red-400">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                                                {{ __('message.delete') }}
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="shadcn-table-td text-center">
                                            {{ __('message.no_data_found') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="py-4">
                    {{ $vehicles->appends(request()->query())->links('pagination.shadcn-pagination') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Select all checkbox functionality
        $('#selectAll').on('change', function() {
            $('input[name="vehicle_checkbox"]').prop('checked', $(this).prop('checked'));
        });

        // Dropdown menu toggle
        $(document).on('click', '.shadcn-button-ghost', function() {
            $(this).next('.shadcn-dropdown-menu').toggleClass('hidden');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.shadcn-dropdown-menu').addClass('hidden');
            }
        });
    });
</script>
@endsection
