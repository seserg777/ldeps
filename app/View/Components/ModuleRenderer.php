<?php

namespace App\View\Components;

use App\Models\Module;
use Illuminate\View\Component;

class ModuleRenderer extends Component
{
    public $position;
    public $activeMenuId;
    public $modules;

    /**
     * Create a new component instance.
     *
     * @param string $position
     * @param int|null $activeMenuId
     * @return void
     */
    public function __construct(string $position, ?int $activeMenuId = null)
    {
        $this->position = $position;
        $this->activeMenuId = $activeMenuId;
        $this->modules = $this->loadModules();
    }

    /**
     * Load modules for the given position and active menu.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function loadModules(): \Illuminate\Support\Collection
    {
        $query = Module::published()
            ->position($this->position)
            ->ordered()
            ->with('menuItems');

        $modules = $query->get();

        // Filter modules based on menu assignment
        return $modules->filter(function ($module) {
            // If module has no menu items assigned, show on all pages
            if ($module->menuItems->isEmpty()) {
                return true;
            }

            // If no active menu ID, don't show modules with menu restrictions
            if ($this->activeMenuId === null) {
                return false;
            }

            // Check if active menu ID is in module's assigned menu items
            return $module->menuItems->contains('id', $this->activeMenuId);
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.module-renderer');
    }
}

