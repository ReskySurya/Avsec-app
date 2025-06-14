<?php

namespace App\Views\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormWTMD extends Component
{

    public $wtmdLocations;

    public function __construct($wtmdLocations = null)
    {
        $this->wtmdLocations = $wtmdLocations;
    }

    public function render(): View|Closure|string
    {
        return view('components.wtmd-form', [
            'wtmdLocations' => $this->wtmdLocations
        ]);
    }
}
