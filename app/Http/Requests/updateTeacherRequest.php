<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class updateTeacherRequest extends FormRequest
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
            "name" => "sometimes|max:255",
            "email" => "sometimes|email|min:6|max:255",
            "password" => "sometimes|min:6|max:255",
            "role" => "sometimes|in:teacher",
        ];
    }

    public function messages(): array
    {
        return [
            "name.sometimes" => "Enter a valid name",
            "name.max" => "Words limit exceeds, Enter a smaller name of not more than 255 characters",

            "email.sometimes" => "Email is required",
            "email.email" => "Email must be in proper format like example@gmail.com",
            "email.min" => "Email too short, Please enter a big email",
            "email.max" => "Email size too long, Please enter a smaller email",

            "password.sometimes" => "Please enter a password",
            "password.min" => "Password too short, Please enter a password more than 6 characters",
            "password.max" => "Password too long, Please enter a smaller password",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            "success" => false,
            "message" => "Some validation errors has occured, Please check:",
            "errors" => $validator->errors()->all(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
