<?php

namespace App\Http\Resources;

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
            'parent' => $this->parent->name ?? '',
            'type' => $this->payment_type ?? '',
            'student' => $this->recipient->name ?? '',
            'amount' => $this->amount ?? '',
            'status' => $this->status ?? '',
            'paid_at' => $this->paid_at ?? '',
            'bank' => $this->bank->name ?? '',
            'photo' => $this->proof_of_payment ?? '',
        ];
    }
}
