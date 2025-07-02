<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class loginTeacherRequest extends FormRequest
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
            "email" => "required",
            "password" => "required|min:6",
        ];
    }

    public function messages(): array
    {
        return [
            "email.required" => "Email is must required",
            "email.email" => "Email must be in proper format like example@gamil.com",

            "password.required" => "Password can not be empty, Please enter a password",
            "password.min" => "Password contains at least 6 characters",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => "Some validation errors has occured, Please checek below:",
            "errors" => $validator->errors()->all(),
        ], 422));
    }

}
