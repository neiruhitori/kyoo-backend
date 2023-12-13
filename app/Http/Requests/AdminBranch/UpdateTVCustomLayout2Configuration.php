<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateTVCustomLayout2Configuration extends FormRequest
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
            'running_text' => 'required|string|max:100'
        ];
    }
}
