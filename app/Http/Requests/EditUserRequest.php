<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
     protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $response = response()->json([
            "response" => [
                "message" => $errors,
                "status"  => 422, 
            ]
        ], 422); 

           throw new \Illuminate\Validation\ValidationException($validator, $response);
    }   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $this->user()->id,
            'password' => 'sometimes|string|min:6|confirmed',
            'phone' => 'sometimes|string|max:15',
            'status' => 'sometimes|in:0,1',
        ];
    }
}
