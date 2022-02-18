<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public $class;
    public $icon;
    public $style;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($icon = null, $class = '', $style = '')
    {
        $this->class = $class;
        $this->icon = $icon;
        $this->style = $style;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.icon');
    }
}
