<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class StoreDirectQueue extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
        public function authorize()
        {
            $allowedAction = array('cs', 'spv', 'device');
            return in_array(Auth::user()->role, $allowedAction);
        }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vct_id' => 'required|exists:users,id',
            'workstation_service_id' => 'required|exists:workstation_services,id',
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable'
        ];
    }
}
