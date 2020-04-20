<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationBranch extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:5',
            'industry_category_id' => 'required|exists:industry_categories,id',
            'email' => 'required|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
            'country' => 'required|string',
            'phone' => 'required|numeric|min:5',
            'regency_id' => 'required|exists:indoregion_regencies,id',
            'g-recaptcha-response' => 'required|captcha',
        ];
    }
}
