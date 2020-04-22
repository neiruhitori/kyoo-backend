<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class StoreSlot extends FormRequest
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
            'max_slots' => 'required|numeric|min:1',
            'day' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time'
        ];
    }
}
