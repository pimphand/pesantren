<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SantriResource extends JsonResource
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
            'uuid' => $this->uuid,
            'username' => $this->username,
            'phone ' => $this->phone,
            'name' => $this->name,
            'email' => $this->email,
            'balance' => $this->balance,
            'parent' => $this->parentDetail->username,
        ];
    }
}
