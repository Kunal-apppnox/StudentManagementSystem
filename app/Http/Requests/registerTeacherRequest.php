<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class registerTeacherRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "email" => "required|email|min:6|max:255|unique:users",
            "password" => "required|min:6|max:255",
            "role" => "required|in:teacher",
        ];
    }
    public function messages(): array
    {
        return [
            "name.required" => "Name can not be null, Please enter a name",
            "name.max" => "Word limit exceed, please enter a name of not more than 255 words",
            "name.string" => "Name must be valid string.",

            "email.required" => "Email can not be empty, Please enter a email",
            "email.email" => "Email must be in email format like example@gmail.com",
            "email.min" => "Email must contains 6 characters",
            "email.max" => "Email can not contains more than 255 characters",
            "email.unique" => "This email is already taken , Please enter a unique email",

            "password.required" => "Please enter the password",
            "password.min" => "Password must contains 6 characters",
            "password.max" => "Enter small password, Password can not be more than 255 characters",

            "role.required" => "Role not declared, Please declare role",
            "role.in" => "Role should be only teacher",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => "Some validation error has occured, Please check below:",
            "errors" => $validator->errors()->all(),
        ], 422));
    }
}
