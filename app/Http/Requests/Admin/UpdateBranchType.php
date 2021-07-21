<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateBranchType extends FormRequest
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
            'code' => 'required|string|unique:branch_types,code,'.$this->id,
            'name' => 'required|string|unique:branch_types,name,'.$this->id,
            'is_premium' => 'required|boolean',
            'is_appointment' => 'required|boolean',
            'is_direct_queue' => 'required|boolean',
        ];
    }
}
