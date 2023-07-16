<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class StoreWorkstationService extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array(Auth::user()->role, ['admin_branch', 'cs']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'workstation_id' => 'required|exists:workstations,id',
            'service_id' => 'required|exists:services,id',
            'priority' => 'required|min:1|max:5',
        ];
    }
}
