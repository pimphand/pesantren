<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if the user has permission to view the resource
        $userCreated = \App\Models\User::find($this->created_by);
        $userUpdated = \App\Models\User::find($this->updated_by);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_by' => $userCreated ? $userCreated->name : null,
            'updated_by' => $userUpdated ? $userUpdated->name : null,
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
