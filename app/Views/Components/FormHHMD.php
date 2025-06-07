<?php

namespace App\Views\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Location;
use App\Models\Equipment;

class FormHHMD extends Component
{
    public $hhmdLocations;
    public $hhmdEquipment;

    public function __construct()
    {
        // Ambil equipment HHMD
        $this->hhmdEquipment = Equipment::where('name', 'hhmd')->first();

        // Ambil location dengan id 1-6 yang terhubung dengan HHMD
        if ($this->hhmdEquipment) {
            $this->hhmdLocations = $this->hhmdEquipment->locations()
                ->wherePivot('equipment_id', $this->hhmdEquipment->id)
                ->whereIn('locations.id', [1, 2, 3, 4, 5, 6])
                ->withPivot('description')
                ->withTimestamps()
                ->orderBy('locations.id')
                ->get();
        } else {
            $this->hhmdLocations = collect();
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.hhmd-form');
    }
}
