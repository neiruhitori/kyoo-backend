<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class StoreAppointment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->role == 'cs';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'slot_id' => 'required|exists:slots,id',
            'date' => 'required|date|after_or_equal:today',
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'notes' => 'nullable'
        ];
    }
}
