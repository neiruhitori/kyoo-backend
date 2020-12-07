<?php

namespace App\Http\Requests\Admin;

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
        return Auth::user()->role == 'admin_kyoo';
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
            'name' => 'required|string',
            'industry_category_id' => 'required|exists:industry_categories,id',
            'branch_type_id' => 'required|exists:branch_types,id',
            'description' => 'nullable|string',
            'email' => 'email',
            'country' => 'required',
            'fixed_phone' => 'nullable|numeric|min:5',
            'mobile_phone' => 'required|numeric|min:5',
            'logo' => 'sometimes|image|max:2048',
            'photo' => 'sometimes|image|max:2048',
            'is_active' => 'required',

            // branch location
            'regency_id' => 'required|exists:indoregion_regencies,id',
            'address' => 'required|string',
            'lat' => 'required',
            'long' => 'required',

            // branch admin
            'admin_name' => 'required|string',
            'admin_email' => 'required|email',
            'admin_phone' => 'required|numeric|min:5',
            'admin_phone' => 'required|numeric|min:5',
        ];
    }
}
