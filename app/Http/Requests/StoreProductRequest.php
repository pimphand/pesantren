<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'stock' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kolom nama produk harus diisi.',
            'price.required' => 'Kolom harga harus diisi.',
            'stock.required' => 'Kolom stok harus diisi.',
            'price.min' => 'Harga tidak boleh kurang dari Rp.0',
            'price.max' => 'Harga tidak boleh lebih dari Rp. 9.999.999.999',
            'stock.min' => 'Stok tidak boleh kurang dari 0',
            'stock.max' => 'Stok tidak boleh lebih dari 10.000.000',
            'category_id.required' => 'Kolom kategori harus diisi.',
            'photo.image' => 'Kolom foto harus berupa gambar.',
        ];
    }
}
