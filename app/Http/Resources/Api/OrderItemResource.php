<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->product->name,
            'category' => $this->product->category->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total ?: $this->price * $this->quantity,
        ];
    }
}
