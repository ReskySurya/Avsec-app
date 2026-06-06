<?php

namespace App\Http\Controllers\Api\V1\DailyTest;

use App\Http\Controllers\Controller;
use App\Models\EquipmentLocation;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportStatus;
use App\Traits\ApiResponse;
use App\Http\Resources\ReportResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class XrayController extends Controller
{
    use ApiResponse;

    public function indexCabin()
    {
        return $this->getLocations('xraycabin');
    }

    public function indexBagasi()
    {
        return $this->getLocations('xraybagasi');
    }

    private function getLocations($equipmentName)
    {
        $equipment = Equipment::where('name', $equipmentName)->first();
        
        if (!$equipment) {
            return $this->errorResponse("Equipment $equipmentName tidak ditemukan", 404);
        }

        $locations = EquipmentLocation::where('equipment_id', $equipment->id)
            ->with('location')
            ->get()
            ->map(function ($el) {
                return [
                    'id' => $el->id,
                    'location_id' => $el->location_id,
                    'location_name' => $el->location->name ?? 'Tidak tersedia',
                    'merk_type' => $el->merk_type ?? 'Tidak tersedia',
                    'certificate_info' => $el->certificateInfo ?? 'Tidak tersedia',
                ];
            });

        return $this->successResponse($locations, "Data lokasi $equipmentName berhasil diambil");
    }
}
