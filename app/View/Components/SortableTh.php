<?php
namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SortableTh extends Component
{
    public function __construct(public bool $sortable = false)
    {}

    public function render()
    {
        return view('components.sortable-th');
    }
}
