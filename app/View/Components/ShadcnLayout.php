<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ShadcnLayout extends Component
{
    /**
     * The assets to include.
     *
     * @var array
     */
    public $assets;

    /**
     * Create a new component instance.
     *
     * @param array $assets
     * @return void
     */
    public function __construct($assets = [])
    {
        $this->assets = $assets;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Get theme color from settings
        $themeColor = SettingData('app_content', 'site_color') ?? '#3490dc';
        
        return view('layouts.shadcn-master', [
            'themeColor' => $themeColor,
        ]);
    }
}
