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
            'name' => 'required|string',
            'industry_category_id' => 'required|exists:industry_categories,id',
            'description' => 'required',
            'email' => 'email',
            'country' => 'required',
            'timezone' => 'required|string',
            'fixed_phone' => 'nullable|numeric|min:5',
            'mobile_phone' => 'required|numeric|min:5',
            'logo' => 'sometimes|image',
            'photo' => 'sometimes|image',
            'is_active' => 'required',

            // branch location
            'regency_id' => 'required|exists:indoregion_regencies,id',
            'address' => 'required|string',
            'lat' => 'required',
            'long' => 'required',
        ];
    }
}
