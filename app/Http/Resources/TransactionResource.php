<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "accountId" => $this->account_id,
            "targetAccountId" => $this->target_account_id,
            "categoryId" => $this->category_id,
            "type" => $this->type,
            "amount" => (float) $this->amount,
            "date" => $this->date,
            "notes" => $this->notes,
            "account" => new AccountResource($this->whenLoaded('account')),
            "targetAccount" => new AccountResource($this->whenLoaded('targetAccount')),
            "category" => new CategoryResource($this->whenLoaded('category')),
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];
    }
}
