<?php
namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    protected array $scripts;

    /**
     * Create a new component instance.
     */
    public function __construct(public string $title = 'Home', array $scripts = [])
    {
        $this->scripts = $scripts;
    }

    public function setPathes(string $dir = 'resources', array $pathes = ['css/app.css', 'js/app.js']): array
    {
        return array_map(fn($p) => "{$dir}/$p", $pathes, $this->scripts);
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
