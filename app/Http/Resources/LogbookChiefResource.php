<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogbookChiefResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'team' => $this->team,
            'shift' => $this->shift,
            'chief_name' => $this->chief_name,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'kemajuan' => $this->whenLoaded('kemajuan'),
            'creator' => [
                'id' => $this->created_by,
                'name' => $this->createdBy->name ?? null,
            ],
            'approver' => [
                'id' => $this->approved_by,
                'name' => $this->approvedBy->name ?? null,
            ],
        ];
    }
}
