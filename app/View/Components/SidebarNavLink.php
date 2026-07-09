<?php
namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarNavLink extends Component
{
    public function __construct(
        public string $route,
        public string $icon,
        public string $label,
    ) {}

    public function render()
    {
        return view('components.sidebar-nav-link');
    }
}
