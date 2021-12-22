<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ManufacturerCreateRequest extends FormRequest
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
            'email' => [
                'required',
                'email' => 'email:rfc',function($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                       $fail('Enter a valid email address.');
                    }
                },
                Rule::unique('manufacturers')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })->ignore($this->id)
            ],
            'name' => 'required|string|max:100',
            'mobile' => 'required',
            'zipcode' => 'present|nullable|min:5|max:7',
            // 'address' => 'required',
            'country_id'=>'required|after_or_equal:1',
            'state_id' => [
                'required',
                Rule::exists('states','id')->where(function ($query) {
                    return $query->where('country_id', $this->input('country_id'));
                }),
            ],
            'city_id' => [
                'required',
                Rule::exists('cities','id')->where(function ($query) {
                    return $query->where('state_id', $this->input('state_id'));
                }),
            ],
            // 'state_id' => 'required|after_or_equal:1',
            // 'city_id' => 'required|after_or_equal:1',
            // 'zipcode' => 'required|min:5|max:7',
            'logo' => 'required|mimes:png,jpeg,jpg,svg,webp|max:2048'
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
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already exist.',
            'email.email' => 'Invalid Email.',
            'name.required' => 'Name is required.',
            'mobile.required'=> 'Mobile is required.',
            'logo.required'=> 'Logo is required.',
            'zipcode.required' => 'Zip code is required.',
            'zipcode.min' => 'Zip code must be at least :min characters.',
            'zipcode.max' => 'Zip code must not be greater than :max characters.'  
            // 'state_id.required'=> 'Please select state.',
            // 'city_id.required'=> 'Please select city.',         
        ];
    }
}
