<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id_hash,
            "idHash" => $this->id_hash,
            "name" => $this->name,
            "type" => $this->type,
            "balance" => (float) $this->balance,
            "currency" => $this->currency,
            "color" => $this->color,
            "icon" => $this->icon,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];
    }
}
