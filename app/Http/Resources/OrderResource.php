<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'customer' => [
                'id' => $this->user->uuid,
                'name' => $this->user->name,
            ],
            'invoice_number' => $this->invoice_number,
            'total' => $this->total,
            'tax' => $this->tax,
            'date' => date('d-m-Y', strtotime($this->created_at)),
            'time' => date('H:i', strtotime($this->created_at)),
            'status' => $this->status,
            'payment' => [
                'method' => $this->payment->payment_method,
            ],
            'items' => $this->orderItems->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ];
            }),
        ];
    }
}
