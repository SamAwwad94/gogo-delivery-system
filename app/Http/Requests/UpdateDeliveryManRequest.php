<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeliveryManRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('deliveryman')),
            ],
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
            'contact_number' => 'sometimes|required|string',
            'user_type' => 'sometimes|required|string|in:delivery_man',
            'status' => 'sometimes|required|numeric|in:0,1',
            'address' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'is_verified_delivery_man' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
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
            'name' => __('message.name'),
            'email' => __('message.email'),
            'username' => __('message.username'),
            'password' => __('message.password'),
            'contact_number' => __('message.contact_number'),
            'user_type' => __('message.user_type'),
            'status' => __('message.status'),
            'address' => __('message.address'),
            'country_id' => __('message.country'),
            'city_id' => __('message.city'),
            'profile_image' => __('message.profile_image'),
            'vehicle_id' => __('message.vehicle'),
            'is_verified_delivery_man' => __('message.is_verified_delivery_man'),
            'is_online' => __('message.is_online'),
            'is_available' => __('message.is_available'),
        ];
    }
}
