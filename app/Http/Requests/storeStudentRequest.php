<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'age' => 'required|integer',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            "name.required" => "Name is required, Please enter a name.",
            "name.string" => "Name is not in the correct format, correct format is string.",
            "name.max" => "Name is too long, Please enter a short name.",
            "email.required" => "Email can not be null, Please enter a email",
            "email.email" => "Email must be in proper format like example@gmail.com",
            "email.unique" => "Email must be unique. This email is already taken.",
            "age.required" => "Age is required and must be a number.",
            "age.integer" => "Age must be an integer.",
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => "Some validation errors has occured, Please check:",
            "errors" => $validator->errors()->all(),
        ], 422));
    }
}
