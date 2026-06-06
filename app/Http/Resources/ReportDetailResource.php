<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Return dynamic fields depending on equipment type
        // The table is wide (60+ columns), so we return all attributes
        // but exclude timestamps and PKs if desired
        $attributes = parent::toArray($request);
        
        return $attributes;
    }
}
