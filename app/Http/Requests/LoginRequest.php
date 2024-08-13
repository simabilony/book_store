<?php

namespace App\Http\Requests;

use App\Service\ApiResponseService;
use Dotenv\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|string|email|max:20|unique:users,email',
            'password' => 'required|string|min:8|max:20',
        ];
    }
    protected function prepareForValidation(Validator $validator)
    {
        $errors= $validator->errors()->all();
        throw new \HttpRequestException(ApiResponseService::error('valedation errors',422,$errors));
    }
}
