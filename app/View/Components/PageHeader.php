<?php
namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    // public string $welcome, $title, $subtitle;

    public function __construct(
        public string $title,
        public string $subtitle = 'Overview of your application',
        public string $welcome = 'សូមស្វាគមន៍មកកាន់ទំព័រ'
    ) {}

    public function render()
    {
        return view('components.page-header');
    }
}
