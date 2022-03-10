<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateBranch extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->role == 'admin_branch';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // branch profile
            'name' => 'required_with:name|string',
            'industry_category_id' => 'required_with:industry_category_id|exists:industry_categories,id',
            'description' => 'required_with:description',
            'email' => 'email',
            'country' => 'required_with:country',
            'timezone' => 'required_with:timezone|string',
            'fixed_phone' => 'nullable|numeric|min:5',
            'mobile_phone' => 'required_with:mobile_phone|numeric|min:5',
            'logo' => 'sometimes|image',
            'photo' => 'sometimes|image',
            'is_active' => 'required_with:is_active',

            // branch location
            'regency_id' => 'required_with:regency_id|exists:indoregion_regencies,id',
            'address' => 'required_with:address|string',
            'lat' => 'required_with:lat',
            'long' => 'required_with:long',
        ];
    }
}
