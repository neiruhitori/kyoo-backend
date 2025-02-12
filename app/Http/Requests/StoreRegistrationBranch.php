<?php

namespace App\Http\Requests;

use App\Models\Regency;
use App\Models\SGRegencies;
use App\Models\VNRegencies;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationBranch extends FormRequest
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
            'name' => 'required|string|min:5',
            'industry_category_id' => 'required|exists:industry_categories,id',
            'email' => 'required|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
            'country' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|numeric|min:5',
            'regency_id' => ['required',function($attribute, $value, $fail){
                $this->validateRegencyId($attribute, $value, $fail);
            }],
            'accept_term_condition' => 'required'
        ];
    }
    public function validateRegencyId($attribute, $value, $fail)
        {
            $existsInIndo = Regency::where('id', $value)->exists();
            $existsInVN = VNRegencies::where('id', $value)->exists();
            $existsInSG = SGRegencies::where('id', $value)->exists();

            if (!$existsInIndo && !$existsInVN && !$existsInSG) {
                $fail(__('The selected regency does not exist in any region.'));
            }
        }
}
