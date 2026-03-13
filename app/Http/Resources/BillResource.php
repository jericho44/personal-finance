<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_hash,
            'idHash' => $this->id_hash,
            'name' => $this->name,
            'amount' => (float) $this->amount,
            'dueDate' => $this->due_date,
            'frequency' => $this->frequency,
            'isPaid' => (bool) $this->is_paid,
            'categoryId' => $this->category_id,
            'notes' => $this->notes,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
