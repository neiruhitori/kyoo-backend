<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExhibition extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'user_id' => 'required|exists:users,id',
            'slot_id' => 'required|exists:slots,id',
            'date' => 'required|date|after_or_equal:today',
            'name' => 'required|string',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'notes' => 'nullable'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Failed to store booking',
            'data' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
