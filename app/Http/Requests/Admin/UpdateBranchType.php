<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

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
            'code' => [
                'required',
                'string',
                Rule::unique('branch_types')
                    ->ignore($this->id)
                    ->where(fn ($query) => $query->whereNull('deleted_at'))
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('branch_types')
                    ->ignore($this->id)
                    ->where(fn ($query) => $query->whereNull('deleted_at'))
            ],
            'is_premium' => 'required|boolean',
            'is_appointment' => 'required|boolean',
            'is_direct_queue' => 'required|boolean',
            'is_exhibition' => 'required|boolean'
        ];
    }
}
