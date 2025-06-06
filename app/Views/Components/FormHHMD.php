<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormHHMD extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Constructor kosong karena tidak ada parameter yang diperlukan
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.hhmd.form-hhmd');
    }
}
