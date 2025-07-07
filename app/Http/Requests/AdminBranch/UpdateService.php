<?php

namespace App\Http\Requests\AdminBranch;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateService extends FormRequest
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

    protected function isRequiredPrefixQueue()
    {
        return Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->Branch->hasAccess('Panggilan Suara');
    }

    protected function isRequiredSLADuration()
    {
        return Auth::user()->Branch->BranchType->is_direct_queue && Auth::user()->Branch->BranchType->is_premium;
    }
    protected function isAppointment()
    {
        return Auth::user()->Branch->BranchType->is_appointment;
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'service_category_id' => $this->isAppointment() ? 'exists:service_categories,id' : 'nullable',
            'prefix_queue' => $this->isRequiredPrefixQueue() ? 'required|alpha_num' : 'nullable|alpha_num',
            'sla_duration' => $this->isRequiredSLADuration() ? 'numeric|min:0' : 'numeric',
            'is_show' => 'boolean',
            'kiosk_name' => 'nullable|string',
        ];
    }
}
