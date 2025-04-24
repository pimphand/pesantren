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
                'photo' => $this->student->photo ?? null,
                'phone' => $this->phone ?? null,
                'level' => $this->student->level ?? null,
                'class_now' => $this->student->class_now ?? null,
                'admission_number' => $this->student->admission_number ?? null,
                'national_admission_number' => $this->student->national_admission_number ?? null,
                'place_of_birth' => $this->student->place_of_birth ?? null,
                'date_of_birth' => $this->student->date_of_birth ?? null,
                'gender' => $this->student->gander ?? null,
                'address' => $this->student->address ?? null,
            ],
            'balance_histories' => $this->when(
                $this->relationLoaded('balanceHistories'),
                fn () => BalanceHistory::collection($this->balanceHistories->load('order'))
            ),
        ];
    }
}
