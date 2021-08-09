<?php

namespace App\Http\Requests;

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
            // 'email' => 'required|email|unique:users',
            'email' => [
                'required',
                Rule::unique('users', 'email')
                ->where(static function ($query) {
                    return $query->whereNull('deleted_at');
                })
                ->ignore($this->user),
            ],
            'name' => 'required|string|max:100',            
            'mobile' => 'required',
            'zipcode' => 'min:5|max:7',
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
            'mobile.required'=> 'Mobile is required!',
        ];
    }
}
