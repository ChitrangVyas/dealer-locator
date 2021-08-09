<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetailerUserUpdateRequest extends FormRequest
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
            'email' => 'required|email',
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
            'email.required' => 'Email is required!',
            'email.unique' => 'Email is already exist.',
            'email.email' => 'Invalid Email.',
            'name.required' => 'Name is required!',
            'password.required' => 'Password is required!',
            'mobile.required'=> 'Mobile is required!',
            'address.required'=> 'Address is required!',
            'state.required'=> 'State is required!',
            'city.required'=> 'City is required!',
            'zipcode.required' => 'Zipcode is required!'
        ];
    }
}
