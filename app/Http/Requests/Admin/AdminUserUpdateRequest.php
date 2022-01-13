<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return true;
        return Auth::check();
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
                'email' => 'email:rfc', function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Enter a valid email address.');
                    }
                },
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNull('deleted_at')->where('users_group_id', '=', $this->input('user_group'));
                })->ignore($this->id)
            ],
            'name' => 'required|string|max:100',
            'password' => 'present|nullable|min:6|max:16',
            'mobile'=>'required|digits_between:10,12',
            'address' => 'required',
            'country_id' => 'required|after_or_equal:1',
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
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already exist.',
            'email.email' => 'Invalid Email.',
            'password.min' => 'Password must be at least :min characters.',
            'password.max' => 'Password must not be greater than :max characters.',
            'name.required' => 'Name is required.',
            'address.required' => 'Address is required.',
            'country_id.required' => 'Country is required',
            'mobile.required' => 'Mobile is required.',
            'state.required' => 'State is required.',
            'city.required' => 'City is required.',
            'zipcode.required' => 'Zip code is required.',
            'zipcode.min' => 'Zip code must be at least :min characters.',
            'zipcode.max' => 'Zip code must not be greater than :max characters.'
        ];
    }
}
