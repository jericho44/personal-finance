<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_hash,
            'idHash' => $this->id_hash,
            'name' => $this->name,
            'targetAmount' => (float) $this->target_amount,
            'currentAmount' => (float) $this->current_amount,
            'deadline' => $this->deadline,
            'color' => $this->color,
            'notes' => $this->notes,
            'isCompleted' => (bool) $this->is_completed,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
