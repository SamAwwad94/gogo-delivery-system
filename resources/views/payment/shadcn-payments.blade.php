@extends('layouts.app')
@section('title', __('message.payment'))
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? trans('message.payment') }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('payment.index', ['classic' => 1]) }}" class="shadcn-button shadcn-button-outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ __('message.classic_view') }}
                                </a>
                            </div>
                        </div>

                        <!-- ShadCN Table Filters -->
                        <div class="p-3 border-bottom">
                            <form action="{{ route('refactored-payment.index') }}" method="GET" class="shadcn-filters">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="payment_type" class="form-label">{{ __('message.payment_type') }}</label>
                                        <select name="payment_type" id="payment_type" class="form-control shadcn-select">
                                            <option value="">{{ __('message.all') }}</option>
                                            <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>{{ __('message.cash') }}</option>
                                            <option value="online" {{ request('payment_type') == 'online' ? 'selected' : '' }}>{{ __('message.online') }}</option>
                                            <option value="wallet" {{ request('payment_type') == 'wallet' ? 'selected' : '' }}>{{ __('message.wallet') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="payment_status" class="form-label">{{ __('message.payment_status') }}</label>
                                        <select name="payment_status" id="payment_status" class="form-control shadcn-select">
                                            <option value="">{{ __('message.all') }}</option>
                                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>{{ __('message.pending') }}</option>
                                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('message.paid') }}</option>
                                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>{{ __('message.failed') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="from_date" class="form-label">{{ __('message.from_date') }}</label>
                                        <input type="date" name="from_date" id="from_date" class="form-control shadcn-input" value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="to_date" class="form-label">{{ __('message.to_date') }}</label>
                                        <input type="date" name="to_date" id="to_date" class="form-control shadcn-input" value="{{ request('to_date') }}">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="shadcn-button shadcn-button-primary mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                                        {{ __('message.filter') }}
                                    </button>
                                    <a href="{{ route('refactored-payment.index') }}" class="shadcn-button shadcn-button-outline">
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
                                        <th>{{ __('message.id') }}</th>
                                        <th>{{ __('message.order') }}</th>
                                        <th>{{ __('message.client') }}</th>
                                        <th>{{ __('message.payment_type') }}</th>
                                        <th>{{ __('message.total_amount') }}</th>
                                        <th>{{ __('message.payment_status') }}</th>
                                        <th>{{ __('message.datetime') }}</th>
                                        <th>{{ __('message.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($payments->count() > 0)
                                        @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->id }}</td>
                                                <td>
                                                    @if($payment->order)
                                                        <a href="{{ route('order.show', $payment->order_id) }}" class="text-primary">
                                                            #{{ $payment->order_id }}
                                                        </a>
                                                    @else
                                                        #{{ $payment->order_id }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->client)
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ getSingleMedia($payment->client, 'profile_image', null) }}" alt="client" class="avatar-40 rounded-circle">
                                                            <div class="ml-2">
                                                                <h6 class="mb-0">{{ $payment->client->display_name ?? '' }}</h6>
                                                                <p class="mb-0">{{ $payment->client->email ?? '' }}</p>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{ __('message.not_found') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $payment->payment_type == 'cash' ? 'success' : ($payment->payment_type == 'online' ? 'info' : 'warning') }}">
                                                        {{ __('message.'.$payment->payment_type) }}
                                                    </span>
                                                </td>
                                                <td>{{ getPriceFormat($payment->total_amount) }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $payment->payment_status == 'paid' ? 'success' : ($payment->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                                        {{ __('message.'.$payment->payment_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $payment->datetime ? date('d M Y H:i', strtotime($payment->datetime)) : '' }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('refactored-payment.show', $payment->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                        </a>
                                                        @if(auth()->user()->can('payment-edit'))
                                                            <a href="{{ route('refactored-payment.edit', $payment->id) }}" class="shadcn-button shadcn-button-sm shadcn-button-secondary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->can('payment-delete'))
                                                            <form action="{{ route('refactored-payment.destroy', $payment->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="shadcn-button shadcn-button-sm shadcn-button-destructive" onclick="return confirm('{{ __('message.delete_form_message', ['form' => __('message.payment')]) }}')">
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
                                            <td colspan="8" class="text-center">{{ __('message.no_data_found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $payments->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
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
