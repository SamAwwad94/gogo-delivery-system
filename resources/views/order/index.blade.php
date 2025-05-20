<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.order_list') }}</h5>
                            @if($auth_user->can('order-add'))
                            <a href="{{ route('order.create') }}" class="float-right mr-1 btn btn-sm btn-primary">
                                <i class="fa fa-plus-circle"></i> {{ __('message.add_form_title',['form' => __('message.order')]) }}
                            </a>
                            @endif
                        </div>
                        {{ $dataTable->table(['class' => 'table w-100'],false) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        {{ $dataTable->scripts() }}
    @endsection
</x-master-layout>
