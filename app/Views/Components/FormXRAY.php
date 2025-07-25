<?php

namespace App\Views\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormXRAY extends Component
{
    public $type;
    public $xrayCabinLocations;
    public $xrayBagasiLocations;

    /**
     * Create a new component instance.
     */
    public function __construct($type = 'xrayBagasi', $xrayCabinLocations = null, $xrayBagasiLocations = null)
    {
        $this->type = $type;
        $this->xrayCabinLocations = $xrayCabinLocations;
        $this->xrayBagasiLocations = $xrayBagasiLocations;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if ($this->type === 'xrayCabin') {
            return view('components.xrayCabin-form');
        }

        return view('components.xrayBagasi-form');
    }
}
