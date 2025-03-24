<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*.product' => 'required|exists:products,id',
            'items.*.qty' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $productId = request()->input(str_replace('.qty', '.product', $attribute));
                    $product = \App\Models\Product::whereId($productId)->first();
                    if ($product && $value > $product->stock) {
                        $fail("The quantity requested exceeds available stock.");
                    }
                }
            ],
            'user_id' => 'required|exists:users,uuid',
        ];
    }
}
