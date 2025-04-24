<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductCategoryRequest extends FormRequest
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
        $merchantId = request()->user()->merchant->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories')->where(function ($query) use ($merchantId) {
                    return $query->where('merchant_id', $merchantId)->whereNull('deleted_at');
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori wajib berupa string.',
            'name.max' => 'Nama kategori maksimal 50 karakter.',
            'name.unique' => 'Nama kategori sudah ada.',
        ];
    }
}
