<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class updateStudentrequest extends FormRequest
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
            "name" => "sometimes",
            "email" => "sometimes|email|",
            "age" => "sometimes|integer",
        ];
    }

    public function message(): array
    {
        return [
            "name.sometimes" => "Please enter a valid name.",

            "email.sometimes" => "Please enter a valid email.",
            "email.email" => "Enter email like example@gmail.com",

            "age.sometimes" => "Please enter a valid age.",
            "age.integer" => "Age must be an integer.",
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            "success" => false,
            'message' => "Some validation errors has occured, Please check:",
            "errors" => $validator->errors()->all(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
