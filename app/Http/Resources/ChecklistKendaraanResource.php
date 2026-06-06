<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistKendaraanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'shift' => $this->shift,
            'type' => $this->type,
            'kendaraan_dinas' => $this->kendaraan_dinas,
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'details' => $this->whenLoaded('details'),
            'sender' => [
                'id' => $this->sender_id,
                'name' => $this->sender->name ?? null,
            ],
            'receiver' => [
                'id' => $this->received_id,
                'name' => $this->receiver->name ?? null,
            ],
            'approver' => [
                'id' => $this->approved_id,
                'name' => $this->approver->name ?? null,
            ],
        ];
    }
}
