<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'client_id' => 'required|exists:users,id',
            'pickup_point' => 'required|array',
            'pickup_point.address' => 'required|string',
            'pickup_point.latitude' => 'required|numeric',
            'pickup_point.longitude' => 'required|numeric',
            'pickup_point.contact_number' => 'required|string',
            'delivery_point' => 'required|array',
            'delivery_point.address' => 'required|string',
            'delivery_point.latitude' => 'required|numeric',
            'delivery_point.longitude' => 'required|numeric',
            'delivery_point.contact_number' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'parcel_type' => 'required|string',
            'total_weight' => 'required|numeric',
            'total_distance' => 'required|numeric',
            'pickup_datetime' => 'required|date',
            'delivery_datetime' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'packaging_symbols' => 'nullable',
            'description' => 'nullable|string',
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
            'client_id' => __('message.client'),
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
            'vehicle_id' => __('message.vehicle'),
            'packaging_symbols' => __('message.packaging_symbols'),
            'description' => __('message.description'),
        ];
    }
}
