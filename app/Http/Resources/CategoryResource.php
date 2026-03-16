<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = [
            "id" => $this->id_hash,
            "id_int" => $this->id,
            "idHash" => $this->id_hash,
            "name" => $this->name,
            "type" => $this->type,
            "color" => $this->color,
            "icon" => $this->icon,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];

        return $result;
    }
}
