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
                'email', 
                Rule::unique('users')->where(function ($query) use($request_data) {
                    return $query->whereNull('deleted_at')->whereIn('users_group_id',[$request_data['users_group_id']]);
                })->ignore($request_data['id'])
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
            'users_group_id.required' => 'Contact type is required.'
        ];
    }
}
