<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentResource extends JsonResource
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
            'uuid' => $this->uuid ?? '-',
            'username' => $this->username ?? '-',
            'phone' => $this->phone ?? '-',
            'name' => $this->name ?? '-',
            'email' => $this->email ?? '-',
            'balance' => $this->balance ?? '-',
            'parent_id' => $this->parent_id ?? '-',
            'class_now' => $this->student->class_now ?? '-',
            'photo' => $this->student->photo ?? '-',
            'parent' => $this->parentDetail->username ?? '-',
            'address' => $this->student->address ?? '-',
            'level' => $this->student->level ?? '-',
            'date_of_birth' => $this->student->date_of_birth ?? '-',
            'place_of_birth' => $this->student->place_of_birth ?? '-',
            'gender' => $this->student->gender ?? '-',
            'admission_number' => $this->student->admission_number ?? '-',
            'national_admission_number' => $this->student->national_admission_number ?? '-',
            'created_at' => $this->created_at,
        ];
    }
}
