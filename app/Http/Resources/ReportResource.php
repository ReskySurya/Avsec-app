<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->reportID,
            'test_date' => $this->testDate,
            'equipment_location_id' => $this->equipmentLocationID,
            'device_info' => $this->deviceInfo,
            'certificate_info' => $this->certificateInfo,
            'is_fulfilled' => (bool) $this->isFullFilled,
            'result' => $this->result,
            'note' => $this->note,
            'status' => $this->status ? [
                'id' => $this->status->id,
                'name' => $this->status->name,
                'label' => $this->status->label,
            ] : null,
            'submitted_by' => $this->submittedBy ? [
                'id' => $this->submittedBy->id,
                'name' => $this->submittedBy->name,
                'nip' => $this->submittedBy->nip,
            ] : null,
            'approved_by' => $this->approvedBy ? [
                'id' => $this->approvedBy->id,
                'name' => $this->approvedBy->name,
            ] : null,
            'approval_note' => $this->approvalNote,
            'details' => ReportDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
