<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistSenpiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'waktu' => $this->waktu,
            'nama_pemilik' => $this->nama_pemilik,
            'jenis_senpi' => $this->jenis_senpi,
            'no_senpi' => $this->no_senpi,
            'jumlah_peluru' => $this->jumlah_peluru,
            'keterangan' => $this->keterangan,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'creator' => [
                'id' => $this->created_by,
                'name' => $this->creator->name ?? null,
            ],
            'approver' => [
                'id' => $this->approved_by,
                'name' => $this->approver->name ?? null,
            ],
        ];
    }
}
