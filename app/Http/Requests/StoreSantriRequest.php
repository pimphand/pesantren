<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSantriRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'parent_id' => ['required', 'string', 'max:255'],
            'pin' => ['required', 'string', 'min:6', 'max:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone'],
            'class_now' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'nsm' => ['required', 'string', 'max:255', 'unique:students,admission_number'],
            'nisn' => ['required', 'string', 'max:255', 'unique:students,national_admission_number'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
