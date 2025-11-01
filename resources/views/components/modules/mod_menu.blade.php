{{-- Menu Module Type --}}
@php
    $params = $module->params_array;
    $menuType = $params['menutype'] ?? 'mainmenu';
    
    // Get menu items for this menu type
    $menuItems = \App\Models\Menu\Menu::where('menutype', $menuType)
        ->where('published', 1)
        ->where('parent_id', 0)
        ->orderBy('lft')
        ->with(['children' => function($query) {
            $query->where('published', 1)->orderBy('lft');
        }])
        ->get();
@endphp

@if($menuItems->count() > 0)
    <nav class="module-menu menu-{{ $menuType }}">
        <ul class="menu-list">
            @foreach($menuItems as $item)
                <li class="menu-item menu-item-{{ $item->id }}">
                    <a href="{{ $item->link }}" 
                       class="menu-link {{ $item->children->count() > 0 ? 'has-children' : '' }}">
                        {{ $item->title }}
                    </a>
                    
                    @if($item->children->count() > 0)
                        <ul class="menu-submenu">
                            @foreach($item->children as $child)
                                <li class="menu-item menu-item-{{ $child->id }}">
                                    <a href="{{ $child->link }}" class="menu-link">
                                        {{ $child->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif

