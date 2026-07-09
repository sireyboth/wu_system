<?php
namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTableCard extends Component
{
    public function __construct(
        public string $bodyId = 'table-body',
        public string $paginationId = 'pagination-container',
        public int $colspan = 7,
        public string $loadingText = 'Loading data...',
    ) {}

    public function render()
    {
        return view('components.data-table-card');
    }
}
