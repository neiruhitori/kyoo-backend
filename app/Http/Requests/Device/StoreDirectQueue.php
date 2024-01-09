<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;

class StoreDirectQueue extends FormRequest
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
            'vct_id' => 'required|exists:users,id',
            'workstation_service_id' => 'required|exists:workstation_services,id',
            'name' => 'nullable|string|max:100',
            'phone' => 'nullable'
        ];
    }
}
