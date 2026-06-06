<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogbookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->logbookID,
            'date' => $this->date,
            'location' => [
                'id' => $this->location_area_id,
                'name' => $this->locationArea->name ?? null,
            ],
            'grup' => $this->grup,
            'shift' => $this->shift,
            'status' => $this->status,
            'created_at' => $this->created_at,
            // Include related data if loaded
            'kegiatan' => $this->whenLoaded('details'),
            'personil' => $this->whenLoaded('staff'),
            'fasilitas' => $this->whenLoaded('facilities'),
        ];
    }
}
