<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\ProhibitedItem;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ApiResponse;

class MasterDataController extends Controller
{
    use ApiResponse;

    public function getEquipments()
    {
        $equipments = Equipment::all();
        return $this->successResponse($equipments, 'Data Equipment berhasil diambil');
    }

    public function getLocations()
    {
        $locations = Location::all();
        return $this->successResponse($locations, 'Data Lokasi berhasil diambil');
    }

    public function getProhibitedItems()
    {
        $items = ProhibitedItem::all();
        return $this->successResponse($items, 'Data Prohibited Items berhasil diambil');
    }

    public function getTenants()
    {
        $tenants = Tenant::all();
        return $this->successResponse($tenants, 'Data Tenant berhasil diambil');
    }

    public function getOfficers()
    {
        $officers = User::whereHas('role', function ($query) {
            $query->where('name', 'officer');
        })->get(['id', 'name', 'nip', 'lisensi']);
        
        return $this->successResponse($officers, 'Data Officer berhasil diambil');
    }

    public function getSupervisors()
    {
        $supervisors = User::whereHas('role', function ($query) {
            $query->where('name', 'supervisor');
        })->get(['id', 'name', 'nip']);
        
        return $this->successResponse($supervisors, 'Data Supervisor berhasil diambil');
    }
}
