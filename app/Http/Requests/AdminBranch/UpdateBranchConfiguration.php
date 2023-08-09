<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBranchConfiguration extends FormRequest
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
            'maximum_recall' => 'numeric|min:0',
            'maximum_requeue_count' => 'numeric|min:0',
            'allow_transfer' => 'boolean',
            'phone_owner' => 'numeric|min:5'
        ];
    }
}
