<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title mb-0">{{ $pageTitle ?? 'Users' }}</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            @if($button ?? '')
                                {!! $button !!}
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="mb-4">
                            <form action="{{ route('refactored-client.index') }}" method="GET" class="row align-items-end">
                                <div class="col-md-3 form-group">
                                    <label for="country_id">{{ __('message.country') }}</label>
                                    <select name="country_id" id="country_id" class="form-control select2js">
                                        @foreach($country as $key => $value)
                                            <option value="{{ $key }}" {{ $selectedCountryId == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="city_id">{{ __('message.city') }}</label>
                                    <select name="city_id" id="city_id" class="form-control select2js">
                                        @foreach($cities as $key => $value)
                                            <option value="{{ $key }}" {{ $selectedCityId == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <button type="submit" class="shadcn-button shadcn-button-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                                        {{ __('message.filter') }}
                                    </button>
                                    {!! $reset_file_button !!}
                                </div>
                            </form>
                        </div>

                        <!-- Users Table with ShadCN Styling -->
                        <table id="users-table" class="table transform-to-shadcn">
                            <thead>
                                <tr>
                                    @if($multi_checkbox_delete)
                                    <th data-column-id="checkbox" data-sortable="false" data-filterable="false" width="1%">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="select_all">
                                            <label class="custom-control-label" for="select_all"></label>
                                        </div>
                                    </th>
                                    @endif
                                    <th data-column-id="id" data-type="number">{{ __('message.id') }}</th>
                                    <th data-column-id="profile_image" data-sortable="false" data-filterable="false">{{ __('message.profile_image') }}</th>
                                    <th data-column-id="name">{{ __('message.name') }}</th>
                                    <th data-column-id="email">{{ __('message.email') }}</th>
                                    <th data-column-id="contact_number">{{ __('message.contact_number') }}</th>
                                    <th data-column-id="status" data-type="select" data-options='[
                                        {"label":"Active","value":"active"},
                                        {"label":"Inactive","value":"inactive"},
                                        {"label":"Pending","value":"pending"}
                                    ]'>{{ __('message.status') }}</th>
                                    <th data-column-id="actions" data-sortable="false" data-filterable="false">{{ __('message.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($users) && count($users) > 0)
                                    @foreach($users as $user)
                                        <tr>
                                            @if($multi_checkbox_delete)
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input select_checkbox" id="select_{{ $user->id }}" data-id="{{ $user->id }}">
                                                    <label class="custom-control-label" for="select_{{ $user->id }}"></label>
                                                </div>
                                            </td>
                                            @endif
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <img src="{{ getSingleMedia($user, 'profile_image', null) }}" alt="{{ $user->name }}" class="rounded-full w-10 h-10">
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->contact_number }}</td>
                                            <td>
                                                <span class="status-pill
                                                    @if($user->status == 'active') bg-green-100 text-green-800 @endif
                                                    @if($user->status == 'inactive') bg-red-100 text-red-800 @endif
                                                    @if($user->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                                ">
                                                    {{ ucfirst($user->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="flex space-x-2">
                                                    @if(auth()->user()->can('users-show'))
                                                        <a href="{{ route('refactored-client.show', $user->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-ghost" title="{{ __('message.view') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->can('users-edit'))
                                                        <a href="{{ route('refactored-client.edit', $user->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-ghost" title="{{ __('message.edit') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->can('users-delete'))
                                                        <a href="javascript:void(0)" class="shadcn-button shadcn-button-sm shadcn-button-ghost delete-users" data-id="{{ $user->id }}" title="{{ __('message.delete') }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('message.no_record_found') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/shadcn-table-transformer.js') }}"></script>
    <script>
        $(document).ready(function() {
            // The ShadcnTableTransformer will automatically transform the table
            new ShadcnTableTransformer({
                tableSelector: '#users-table',
                enableFiltering: true,
                enableSorting: true,
                enablePagination: true,
                enableColumnVisibility: true
            });

            // Handle delete button click
            $(document).on('click', '.delete-users', function() {
                var id = $(this).data('id');
                var url = "{{ route('refactored-client.destroy', ':id') }}";
                url = url.replace(':id', id);

                Swal.fire({
                    title: "{{ __('message.are_you_sure') }}",
                    text: "{{ __('message.you_wont_be_able_to_revert_this') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('message.yes_delete_it') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire(
                                    "{{ __('message.deleted') }}",
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    window.location.reload();
                                });
                            }
                        });
                    }
                });
            });

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
                    Swal.fire(
                        "{{ __('message.error') }}",
                        "{{ __('message.please_select_at_least_one_record') }}",
                        'error'
                    );
                    return;
                }

                Swal.fire({
                    title: "{{ __('message.are_you_sure') }}",
                    text: "{{ __('message.you_wont_be_able_to_revert_this') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('message.yes_delete_it') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('datatble.destroySelected') }}",
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: selectedIds,
                                checked_title: 'users-checked'
                            },
                            success: function(response) {
                                Swal.fire(
                                    "{{ __('message.deleted') }}",
                                    response.message,
                                    'success'
                                ).then((result) => {
                                    window.location.reload();
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-master-layout>
