<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormPencatatanPIResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'grup' => $this->grup,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'details' => $this->whenLoaded('details'),
            'creator' => [
                'id' => $this->created_by,
                'name' => $this->creator->name ?? null,
            ],
            'approver' => [
                'id' => $this->approved_id,
                'name' => $this->approver->name ?? null,
            ],
        ];
    }
}
