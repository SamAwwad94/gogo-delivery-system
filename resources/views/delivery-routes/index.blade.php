@extends('layouts.app')
@section('title') {{__('message.delivery_routes')}} @endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-block card-stretch">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                    <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.delivery_routes') }}</h5>
                    @if($auth_user->can('delivery-routes add'))
                    <a href="{{ route('delivery-routes.create') }}" class="float-right mr-1 btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> {{ __('message.add_form_title',['form' => __('message.delivery_route')  ]) }}</a>
                    @endif
                </div>
                {{ $dataTable->table(['class' => 'table  w-100'],false) }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom_script')
    {{ $dataTable->scripts() }}
    <script>
        $(document).on('click', '.delete-route', function() {
            var id = $(this).data('id');
            
            Swal.fire({
                title: "{{ __('message.delete_form', ['form' => __('message.delivery_route')]) }}",
                text: "{{ __('message.confirm_delete', ['form' => __('message.delivery_route')]) }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('message.yes_delete') }}",
                cancelButtonText: "{{ __('message.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.action = "{{ route('delivery-routes.destroy', '') }}/" + id;
                    form.method = 'POST';
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
@endsection
