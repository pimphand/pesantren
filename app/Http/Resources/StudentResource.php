<?php

namespace App\Http\Resources;

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
                'phone' => $this->detail->phone ?? null,
                'photo' => $this->detail->photo ?? null,
                'admission_number' => $this->detail->admission_number ?? null,
                'class_now' => $this->detail->class_now ?? null,
                'class_last' => $this->detail->class_last ?? null,
                'year_of_admission' => $this->detail->year_of_admission ?? null,
                'year_of_leaving' => $this->detail->year_of_leaving ?? null,
            ],
            'balance_histories' => $this->when(
                $this->relationLoaded('balanceHistories'),
                fn () => $this->balanceHistories
            ),
        ];
    }
}
