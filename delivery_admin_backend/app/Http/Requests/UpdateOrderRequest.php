<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'pickup_point' => 'sometimes|required|array',
            'pickup_point.address' => 'sometimes|required|string',
            'pickup_point.latitude' => 'sometimes|required|numeric',
            'pickup_point.longitude' => 'sometimes|required|numeric',
            'pickup_point.contact_number' => 'sometimes|required|string',
            'delivery_point' => 'sometimes|required|array',
            'delivery_point.address' => 'sometimes|required|string',
            'delivery_point.latitude' => 'sometimes|required|numeric',
            'delivery_point.longitude' => 'sometimes|required|numeric',
            'delivery_point.contact_number' => 'sometimes|required|string',
            'country_id' => 'sometimes|required|exists:countries,id',
            'city_id' => 'sometimes|required|exists:cities,id',
            'parcel_type' => 'sometimes|required|string',
            'total_weight' => 'sometimes|required|numeric',
            'total_distance' => 'sometimes|required|numeric',
            'pickup_datetime' => 'sometimes|required|date',
            'delivery_datetime' => 'sometimes|required|date',
            'status' => 'sometimes|required|string',
            'delivery_man_id' => 'sometimes|nullable|exists:users,id',
            'vehicle_id' => 'sometimes|nullable|exists:vehicles,id',
            'packaging_symbols' => 'sometimes|nullable',
            'description' => 'sometimes|nullable|string',
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
            'pickup_point' => __('message.pickup_point'),
            'pickup_point.address' => __('message.pickup_address'),
            'pickup_point.latitude' => __('message.pickup_latitude'),
            'pickup_point.longitude' => __('message.pickup_longitude'),
            'pickup_point.contact_number' => __('message.pickup_contact_number'),
            'delivery_point' => __('message.delivery_point'),
            'delivery_point.address' => __('message.delivery_address'),
            'delivery_point.latitude' => __('message.delivery_latitude'),
            'delivery_point.longitude' => __('message.delivery_longitude'),
            'delivery_point.contact_number' => __('message.delivery_contact_number'),
            'country_id' => __('message.country'),
            'city_id' => __('message.city'),
            'parcel_type' => __('message.parcel_type'),
            'total_weight' => __('message.total_weight'),
            'total_distance' => __('message.total_distance'),
            'pickup_datetime' => __('message.pickup_datetime'),
            'delivery_datetime' => __('message.delivery_datetime'),
            'status' => __('message.status'),
            'delivery_man_id' => __('message.delivery_man'),
            'vehicle_id' => __('message.vehicle'),
            'packaging_symbols' => __('message.packaging_symbols'),
            'description' => __('message.description'),
        ];
    }
}
