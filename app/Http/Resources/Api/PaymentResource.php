<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'recipient' => $this->recipient->name,
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_type' => $this->payment_type,
            'paid_at' => $this->paid_at,
            'proof_of_payment' => $this->proof_of_payment,
            'bank' => $this->bank->name,
        ];
    }
}
