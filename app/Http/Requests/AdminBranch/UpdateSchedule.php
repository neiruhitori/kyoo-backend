<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateSchedule extends FormRequest
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
            'day' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'status' => 'required|in:open,closed,fullday',
            'start_time' => 'required_if:status,open|nullable|date_format:H:i',
            'end_time' => 'required_if:status,open|nullable|date_format:H:i|after:start_time'
        ];
    }
}
