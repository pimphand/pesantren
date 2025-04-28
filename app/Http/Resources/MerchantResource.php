<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?? '-',
            'photo' => $this->merchant->photo ?? '-',
            'address' => $this->merchant->address ?? '-',
            'description' => $this->merchant->description ?? '-',
            'category' => $this->merchant->category ?? '-',
            'is_pin' => $this->merchant->is_pin ?? '-',
            'is_tax' => $this->merchant->is_tax ?? '-',
            'tax' => $this->merchant->tax ?? '-',
            'created_at' => $this->created_at ?? '-',
        ];
    }
}
