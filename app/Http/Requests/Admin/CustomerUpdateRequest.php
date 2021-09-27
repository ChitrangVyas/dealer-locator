<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
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
        $request_data = $this->request->all();
        return [
            // 'email' => 'required|email',
            'email' => [
                'required',
                'email' => 'email:rfc',function($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                       $fail('Please enter valid email.');
                    }
                }
            ],
            'name' => 'required|string|max:100',            
            'mobile' => 'required',
            'address' => 'required',
            'state_id' => 'required|after_or_equal:1',
            'city_id' => 'required|after_or_equal:1',
            'zipcode' => 'required|min:5|max:7',
            'users_group_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required'=>'Name is required.',
            'users_group_id.required'=>'Contact type is required.',
            'email.required'=>'Email is required.',
            'email.email' =>'Please enter valid email.',
            'mobile.required'=>'Mobile is required.',
            'address.required'=>'Address is required.',
            'state_id.required'=>'State is required.',
            'city_id.required'=>'City is required.',
            'zipcode.required'=>'Zip code is required.',
        ];
    }
}
