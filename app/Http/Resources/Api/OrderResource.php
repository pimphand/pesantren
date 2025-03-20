<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'merchant' => $this->merchant->name ?? "Merchant Terhapus",
            'merchant_category' => $this->merchant->category ?? "Merchant Terhapus",
            'total' => $this->total,
            'invoice_number' => $this->invoice_number,
            'items' => OrderItemResource::collection($this->orderItems),
        ];
    }
}
