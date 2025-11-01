<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Menu\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $modules = Module::with('menuItems')
            ->orderBy('ordering', 'asc')
            ->orderBy('position', 'asc')
            ->paginate(20);

        return view('admin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new module.
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $menuItems = Menu::orderBy('title')->get();
        $positions = $this->getAvailablePositions();
        
        return view('admin.modules.create', compact('menuItems', 'positions'));
    }

    /**
     * Store a newly created module in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'note' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'position' => 'required|string|max:50',
            'module' => 'nullable|string|max:50',
            'published' => 'boolean',
            'showtitle' => 'boolean',
            'ordering' => 'nullable|integer',
            'language' => 'nullable|string|max:7',
            'access' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'publish_up' => 'nullable|date',
            'publish_down' => 'nullable|date',
            'params' => 'nullable|string',
            'menu_items' => 'nullable|array',
            'menu_items.*' => 'integer|exists:vjprf_menu,id',
        ]);

        DB::beginTransaction();
        try {
            $module = Module::create([
                'title' => $validated['title'],
                'note' => $validated['note'] ?? '',
                'content' => $validated['content'] ?? null,
                'position' => $validated['position'],
                'module' => $validated['module'] ?? null,
                'published' => $request->boolean('published') ? 1 : 0,
                'showtitle' => $request->boolean('showtitle') ? 1 : 0,
                'ordering' => $validated['ordering'] ?? 0,
                'language' => $validated['language'] ?? '*',
                'access' => $validated['access'] ?? 1,
                'client_id' => $validated['client_id'] ?? 0,
                'publish_up' => $validated['publish_up'] ?? null,
                'publish_down' => $validated['publish_down'] ?? null,
                'params' => $validated['params'] ?? '{}',
            ]);

            // Attach menu items
            if (!empty($validated['menu_items'])) {
                foreach ($validated['menu_items'] as $menuId) {
                    DB::table('vjprf_modules_menu')->insert([
                        'moduleid' => $module->id,
                        'menuid' => $menuId,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.modules.index')
                ->with('success', 'Module created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating module: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified module.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): \Illuminate\View\View
    {
        $module = Module::with('menuItems')->findOrFail($id);
        $menuItems = Menu::orderBy('title')->get();
        $positions = $this->getAvailablePositions();
        
        return view('admin.modules.edit', compact('module', 'menuItems', 'positions'));
    }

    /**
     * Update the specified module in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $module = Module::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'note' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'position' => 'required|string|max:50',
            'module' => 'nullable|string|max:50',
            'published' => 'boolean',
            'showtitle' => 'boolean',
            'ordering' => 'nullable|integer',
            'language' => 'nullable|string|max:7',
            'access' => 'nullable|integer',
            'client_id' => 'nullable|integer',
            'publish_up' => 'nullable|date',
            'publish_down' => 'nullable|date',
            'params' => 'nullable|string',
            'menu_items' => 'nullable|array',
            'menu_items.*' => 'integer|exists:vjprf_menu,id',
        ]);

        DB::beginTransaction();
        try {
            $module->update([
                'title' => $validated['title'],
                'note' => $validated['note'] ?? '',
                'content' => $validated['content'] ?? null,
                'position' => $validated['position'],
                'module' => $validated['module'] ?? null,
                'published' => $request->boolean('published') ? 1 : 0,
                'showtitle' => $request->boolean('showtitle') ? 1 : 0,
                'ordering' => $validated['ordering'] ?? 0,
                'language' => $validated['language'] ?? '*',
                'access' => $validated['access'] ?? 1,
                'client_id' => $validated['client_id'] ?? 0,
                'publish_up' => $validated['publish_up'] ?? null,
                'publish_down' => $validated['publish_down'] ?? null,
                'params' => $validated['params'] ?? '{}',
            ]);

            // Sync menu items
            DB::table('vjprf_modules_menu')->where('moduleid', $module->id)->delete();
            if (!empty($validated['menu_items'])) {
                foreach ($validated['menu_items'] as $menuId) {
                    DB::table('vjprf_modules_menu')->insert([
                        'moduleid' => $module->id,
                        'menuid' => $menuId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Module updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating module: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified module from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $module = Module::findOrFail($id);
            
            // Delete menu associations
            DB::table('vjprf_modules_menu')->where('moduleid', $module->id)->delete();
            
            // Delete module
            $module->delete();

            return redirect()
                ->route('admin.modules.index')
                ->with('success', 'Module deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting module: ' . $e->getMessage());
        }
    }

    /**
     * Get available module positions.
     *
     * @return array
     */
    private function getAvailablePositions(): array
    {
        return [
            'top' => 'Top',
            'left' => 'Left Sidebar',
            'right' => 'Right Sidebar',
            'bottom' => 'Bottom',
            'header' => 'Header',
            'footer' => 'Footer',
            'content-top' => 'Content Top',
            'content-bottom' => 'Content Bottom',
            'breadcrumbs' => 'Breadcrumbs',
            'debug' => 'Debug',
        ];
    }
}

