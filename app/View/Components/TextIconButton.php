<?php
namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextIconButton extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $icon = 'plus',
        public string $label = 'Add new'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.text-icon-button');
    }
}
