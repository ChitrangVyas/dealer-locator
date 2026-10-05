<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DealerUserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [          
            'name' => 'required|string|max:100',
            'address' => 'required',
            'country_id'=>'required|after_or_equal:1',
            'state' => [
                'required',
                Rule::exists('states','id')->where(function ($query) {
                    return $query->where('country_id', $this->input('country_id'));
                }),
            ],
            'city' => [
                'required',
                Rule::exists('cities','id')->where(function ($query) {
                    return $query->where('state_id', $this->input('state'));
                }),
            ],
            'zipcode' => 'required|alpha_num|min:5|max:7',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'mobile.required'=> 'Mobile is required.',
            'address.required'=> 'Address is required.',
            'country_id.required'=>'Country is required',
            'state.required'=> 'State is required.',
            'city.required'=> 'City is required.',
            'zipcode.required' => 'Zip code is required.',
            'zipcode.min' => 'Zip code must be at least :min characters.',
            'zipcode.max' => 'Zip code must not be greater than :max characters.'
        ];
    }
}
