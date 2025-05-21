<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'sometimes|required|exists:orders,id',
            'client_id' => 'sometimes|required|exists:users,id',
            'datetime' => 'sometimes|required|date',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'payment_type' => 'sometimes|required|string|in:cash,online,wallet',
            'txn_id' => 'nullable|string',
            'payment_status' => 'sometimes|required|string|in:pending,paid,failed',
            'transaction_detail' => 'nullable|array',
            'cancel_charges' => 'nullable|numeric|min:0',
            'admin_commission' => 'nullable|numeric|min:0',
            'delivery_man_commission' => 'nullable|numeric|min:0',
            'received_by' => 'nullable|string',
            'delivery_man_fee' => 'nullable|numeric|min:0',
            'delivery_man_tip' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'order_id' => __('message.order'),
            'client_id' => __('message.client'),
            'datetime' => __('message.datetime'),
            'total_amount' => __('message.total_amount'),
            'payment_type' => __('message.payment_type'),
            'txn_id' => __('message.txn_id'),
            'payment_status' => __('message.payment_status'),
            'transaction_detail' => __('message.transaction_detail'),
            'cancel_charges' => __('message.cancel_charges'),
            'admin_commission' => __('message.admin_commission'),
            'delivery_man_commission' => __('message.delivery_man_commission'),
            'received_by' => __('message.received_by'),
            'delivery_man_fee' => __('message.delivery_man_fee'),
            'delivery_man_tip' => __('message.delivery_man_tip'),
        ];
    }
}
