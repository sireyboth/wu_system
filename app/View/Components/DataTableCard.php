<?php
namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTableCard extends Component
{
    public array $columns;

    public function __construct(
        public string $endpoint,
        array $columns,
        public string $defaultSort = 'created_at',
        public string $defaultDir = 'desc',
        public int $perPage = 10,
        public bool $searchable = true,
        public ?string $searchPlaceholder = null,
        public bool $creatable = true,
    ) {
        $this->columns = $this->normalizeColumns($columns);
    }

    /**
     * Fill in defaults for each column so the view never has
     * to guard against missing keys.
     */
    protected function normalizeColumns(array $columns): array
    {
        return collect($columns)->map(fn($col) => array_merge([
            'sortable' => false,
            'align'    => 'left',
        ], $col))->all();
    }

    public function render()
    {
        return view('components.data-table-card');
    }
}
