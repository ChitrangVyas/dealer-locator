<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class RetailerUserCreateRequest extends FormRequest
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
            'dealer' =>'required',
            'email' => [
                'required',
                'email' => 'email:rfc',function($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                       $fail('Enter a valid email address.');
                    }
                },
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNull('deleted_at')->where('users_group_id','=', $this->input('user_group'));
                })->ignore($this->id)
            ],   
            'password' => 'required|min:6|max:16',
            'name' => 'required|string|max:100',            
            'mobile' => 'required',
            'address' => 'required',
            'state' => 'required|after_or_equal:1',
            'city' => 'required|after_or_equal:1',
            'zipcode' => 'required|min:5|max:7',
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
            'dealer.required' => 'Please select a dealer.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already exist.',
            'email.email' => 'Invalid Email.',
            'name.required' => 'Name is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least :min characters.',
            'password.max' => 'Password must not be greater than :max characters.',
            'mobile.required'=> 'Mobile is required.',
            'address.required'=> 'Address is required.',
            'state.required'=> 'State is required.',
            'city.required'=> 'City is required.',
            'zipcode.required' => 'Zip code is required.',
            'zipcode.min' => 'Zip code must be at least :min characters.',
            'zipcode.max' => 'Zip code must not be greater than :max characters.'
        ];
    }
}
