<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'balance' => $this->balance,
            'status' => $this->status,
            'details' => [
                'level' => $this->detail->level ?? null,
                'phone' => $this->detail->phone ?? null,
                'photo' => $this->detail->photo ?? null,
                'admission_number' => $this->detail->admission_number ?? null,
                'national_admission_number' => $this->detail->national_admission_number ?? null,
                'place_of_birth' => $this->detail->place_of_birth ?? null,
                'date_of_birth' => $this->detail->date_of_birth ?? null,
                'gander' => $this->detail->gander ?? null,
                'address' => $this->detail->address ?? null,
            ],
            'balance_histories' => $this->when(
                $this->relationLoaded('balanceHistories'),
                fn () => BalanceHistory::collection($this->balanceHistories->load('order'))
            ),
        ];
    }
}
