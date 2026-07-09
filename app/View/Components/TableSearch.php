<?php
namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableSearch extends Component
{
    public function __construct(
        public string $route,
        public string $placeholder = 'Search...',
        public string $inputId = 'searchInput',
        public string $formId = 'searchForm',
    ) {}

    public function render()
    {
        return view('components.table-search');
    }
}
