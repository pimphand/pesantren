<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Kolom nama produk wajib diisi.',
            'price.required' => 'Kolom harga wajib diisi.',
            'stock.required' => 'Kolom stok wajib diisi.',
            'category_id.required' => 'Kolom kategori wajib diisi.',
            'photo.image' => 'Kolom foto harus berupa gambar.',
        ];
    }
}
