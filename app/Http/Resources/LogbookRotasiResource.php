<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogbookRotasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'details' => $this->whenLoaded('details'),
            'officers' => $this->whenLoaded('officers', function () {
                return $this->officers->map(function ($officer) {
                    return [
                        'id' => $this->id,
                        'officer_id' => $officer->officer_id,
                        'name' => $officer->user->name ?? null,
                        'location' => $officer->location,
                    ];
                });
            }),
        ];
    }
}
