<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
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
            'bank_id' => ['required', 'exists:banks,id','string'],
            'amount' => ['required', 'numeric'],
            'proof_of_payment' => ['nullable','file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'to_user_id' => ['required', 'exists:users,uuid'],
        ];
    }
    public function messages(): array
    {
        return [
            'proof_of_payment.max' => 'Kolom foto tidak boleh lebih dari 2 MB.',
            'to_user_id.required' => 'Kolom penerima harus diisi.',
            'to_user_id.exists' => 'Penerima tidak ditemukan.',
        ];
    }
}
