<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
            'brand_id' => 'required',
            'description' => 'required|min:10',
            'inventory' =>'required',
            'low_stock' => 'required'
            // 'category' => 'required'
        ];
    }
    
    /**
     * messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'brand_id.required' => 'Please select a brand.'
        ];
    }
}
