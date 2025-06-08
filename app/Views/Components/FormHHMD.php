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

    public function __construct($hhmdLocations = null)
    {
        $this->hhmdLocations = $hhmdLocations;
    }

    public function render(): View|Closure|string
    {
        return view('components.hhmd-form', [
            'hhmdLocations' => $this->hhmdLocations
        ]);
    }
}
