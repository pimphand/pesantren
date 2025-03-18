<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceHistory extends JsonResource
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
            'balance' => $this->balance,
            'type' => $this->type,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'description' => $this->description,
            'details' => OrderResource::make($this->whenLoaded('order')),
        ];
    }
}
