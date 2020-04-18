<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
class StoreBranch extends FormRequest
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
            'description' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'fixed_phone' => 'required|numeric|min:5',
            'mobile_phone' => 'nullable|numeric|min:5',
            'logo' => 'required|image',
            'photo' => 'required|image',
            'is_active' => 'required',
            'schedule_template_id' => 'nullable|exists:schedule_templates,id',

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
            'admin_password' => 'required|min:8|confirmed',
        ];
    }
}
