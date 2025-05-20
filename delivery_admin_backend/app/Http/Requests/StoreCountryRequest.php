<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'status' => 'required|numeric|in:0,1',
            'distance_type' => 'required|string|in:km,mile',
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
            'code' => __('message.code'),
            'status' => __('message.status'),
            'distance_type' => __('message.distance_type'),
        ];
    }
}
