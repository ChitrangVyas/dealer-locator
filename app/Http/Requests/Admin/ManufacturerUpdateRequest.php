<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManufacturerUpdateRequest extends FormRequest
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
                       $fail('Please enter valid email.');
                    }
                },
                Rule::unique('manufacturers')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })->ignore($this->id)
            ],
            'name' => 'required|string|max:100',
            'mobile' => 'required',
            // 'address' => 'required',
            // 'state_id' => 'required|after_or_equal:1',
            // 'city_id' => 'required|after_or_equal:1',
            // 'zipcode' => 'required|min:5|max:7',
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
            'email.required' => 'Email is required!',
            'email.unique' => 'Email is already exist.',
            'email.email' => 'Invalid Email.',
            'name.required' => 'Name is required!',
            // 'state_id.required'=> 'Please select state',
            // 'city_id.required'=> 'Please select city',
        ];
    }
}
