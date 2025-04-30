<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMerchantRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:users,name,' . $this->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $this->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone,' . $this->id],
            'pin' => ['nullable', 'string', 'min:6', 'max:6', 'confirmed'],
        ];
    }
}
