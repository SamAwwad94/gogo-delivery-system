<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
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
            'country_id' => 'sometimes|required|exists:countries,id',
            'status' => 'sometimes|required|numeric|in:0,1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
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
            'country_id' => __('message.country'),
            'status' => __('message.status'),
            'latitude' => __('message.latitude'),
            'longitude' => __('message.longitude'),
        ];
    }
}
