<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
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
            "categoryId" => $this->category_id,
            "amount" => (float) $this->amount,
            "startDate" => $this->start_date,
            "endDate" => $this->end_date,
            "isActive" => (bool) $this->is_active,
            "notes" => $this->notes,
            "category" => new CategoryResource($this->whenLoaded('category')),
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];
    }
}
