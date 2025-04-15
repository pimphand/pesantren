<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            /**
             * yang bisa login hanya User dengan role orang_tua
             *
             * @example orang_tua@gmail.com
             */
            'email' => ['required', 'email', 'exists:users,email'],
            /**
             * @example password
             */
            'password' => ['required', 'min:6'],
        ];
    }
    public function messages(): array
    {
        return [
            'email.exists' => 'Email Salah.',
        ];
    }
}
