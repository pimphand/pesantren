<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', self::MAX],
            'username' => ['required', 'string', self::MAX, 'unique:users,' . $this->user->id],
            'email' => ['required', 'string', 'email', self::MAX, 'unique:users,' . $this->user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'pin' => ['nullable', 'string', 'min:6', 'max:6'],
        ];
    }
}
