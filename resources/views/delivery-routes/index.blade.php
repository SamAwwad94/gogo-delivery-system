<x-master-layout>
    <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
    <div class="page-header">
        <div class="page-title">
            <h4>{{ __('message.delivery_routes') }}</h4>
            <h6>{{ __('message.manage_delivery_routes') }}</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('delivery-routes.create') }}" class="btn btn-added">
                <img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-1">
                {{ __('message.add_delivery_route') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-top">
                <div class="search-set">
                    <div class="search-input">
                        <a class="btn btn-searchset"><img src="{{ asset('assets/img/icons/search-white.svg') }}" alt="img"></a>
                    </div>
                </div>
                <div class="wordset">
                    <ul>
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf" href="javascript:void(0);">
                                <img src="{{ asset('assets/img/icons/pdf.svg') }}" alt="img">
                            </a>
                        </li>
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel" href="javascript:void(0);">
                                <img src="{{ asset('assets/img/icons/excel.svg') }}" alt="img">
                            </a>
                        </li>
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="print" href="javascript:void(0);">
                                <img src="{{ asset('assets/img/icons/printer.svg') }}" alt="img">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                        <tr>
                            <th>{{ __('message.id') }}</th>
                            <th>{{ __('message.name') }}</th>
                            <th>{{ __('message.start_location') }}</th>
                            <th>{{ __('message.end_location') }}</th>
                            <th>{{ __('message.deliveryman') }}</th>
                            <th>{{ __('message.status') }}</th>
                            <th>{{ __('message.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($delivery_routes as $delivery_route)
                            <tr>
                                <td>{{ $delivery_route->id }}</td>
                                <td>{{ $delivery_route->name }}</td>
                                <td>{{ $delivery_route->start_location }}</td>
                                <td>{{ $delivery_route->end_location }}</td>
                                <td>{{ optional($delivery_route->deliveryman)->name }}</td>
                                <td>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status_{{ $delivery_route->id }}" class="check"
                                            {{ $delivery_route->status == 'active' ? 'checked' : '' }}
                                            data-id="{{ $delivery_route->id }}">
                                        <label for="status_{{ $delivery_route->id }}" class="checktoggle">checkbox</label>
                                    </div>
                                </td>
                                <td>
                                    <a class="me-3" href="{{ route('delivery-routes.show', $delivery_route->id) }}">
                                        <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="img">
                                    </a>
                                    <a class="me-3" href="{{ route('delivery-routes.map', $delivery_route->id) }}">
                                        <img src="{{ asset('assets/img/icons/map.svg') }}" alt="img">
                                    </a>
                                    <a class="me-3" href="{{ route('delivery-routes.edit', $delivery_route->id) }}">
                                        <img src="{{ asset('assets/img/icons/edit.svg') }}" alt="img">
                                    </a>
                                    <a class="confirm-text" href="javascript:void(0);" onclick="deleteData('delivery-routes', {{ $delivery_route->id }})">
                                        <img src="{{ asset('assets/img/icons/delete.svg') }}" alt="img">
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-box">
                {{ $delivery_routes->links() }}
            </div>
        </div>
    </div>
    <script>
        $(document).on('change', '.check', function() {
            let status = $(this).prop('checked') ? 'active' : 'inactive';
            let id = $(this).data('id');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('delivery-routes.status', '') }}/" + id,
                data: {'status': status, '_token': "{{ csrf_token() }}"},
                success: function(data){
                    if (data.success) {
                        toastr.success('{{ __("message.status_updated") }}');
                    }
                }
            });
        });
    </script>
</x-master-layout>
