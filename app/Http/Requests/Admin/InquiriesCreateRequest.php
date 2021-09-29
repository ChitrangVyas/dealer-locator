<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InquiriesCreateRequest extends FormRequest
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
            'inquiry_source' => 'required|numeric',  
            'customer' => 'required',
                         
        ];
    }

    public function messages()
    {
        return [
            'inquiry_source.required' => 'Inquiry source is required.',
            'customer.required' => 'Customer is required.',
        ];
    }
}
