<div class="flex flex-col h-full">
    <!-- Brand -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
        <div class="flex items-center space-x-2">
            <i class="fas fa-cube text-blue-400 text-xl"></i>
            <span class="text-lg font-semibold text-white">DEPS Admin</span>
        </div>
        <span class="text-xs text-gray-400 bg-gray-800 px-2 py-1 rounded">v1</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <!-- Dashboard -->
        <div class="space-y-1">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                PANEL
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                Dashboard
            </a>
        </div>

        <!-- Catalog -->
        <div class="space-y-1 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                CATALOG
            </div>
            <a href="{{ route('admin.products') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.products*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-box mr-3 text-gray-400"></i>
                Products
            </a>
            <a href="{{ route('admin.categories') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.categories*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-tags mr-3 text-gray-400"></i>
                Categories
            </a>
            <a href="{{ route('admin.manufacturers') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.manufacturers*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-industry mr-3 text-gray-400"></i>
                Manufacturers
            </a>
        </div>

        <!-- Sales -->
        <div class="space-y-1 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                SALES
            </div>
            <a href="{{ route('admin.orders') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.orders*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-shopping-cart mr-3 text-gray-400"></i>
                Orders
            </a>
            <a href="{{ route('admin.customers') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.customers*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-users mr-3 text-gray-400"></i>
                Customers
            </a>
        </div>

        <!-- Menu Management -->
        <div class="space-y-1 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Menu
            </div>
            
            <!-- Menu Types Collapsible -->
            <div x-data="{ open: {{ request()->routeIs('admin.menu.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.menu.*') ? 'bg-gray-800 text-white' : '' }}">
                    <div class="flex items-center">
                        <i class="fas fa-list mr-3 text-gray-400"></i>
                        Menu Types
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-4 space-y-1 mt-1">
                    
                    <a href="{{ route('admin.menu.types.index') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.menu.types*') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-cog mr-2 text-xs"></i>
                        Manage Types
                    </a>
                    
                    @php
                        $menuTypes = \App\Models\Menu\MenuType::orderBy('title')->get();
                    @endphp
                    
                    @if($menuTypes->count() > 0)
                        @foreach($menuTypes as $menuType)
                            <a href="{{ route('admin.menu.items.index', $menuType->menutype) }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.menu.items.index') && request()->route('menutype') == $menuType->menutype ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>
                                {{ $menuType->title }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Management -->
        <div class="space-y-1 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Content
            </div>
            
            <!-- Content Types Collapsible -->
            <div x-data="{ open: {{ request()->routeIs('admin.content.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.content.*') || request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-white' : '' }}">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt mr-3 text-gray-400"></i>
                        Content Management
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="ml-4 space-y-1 mt-1">
                    
                    <a href="{{ route('admin.content.index') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.content.index') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-file-alt mr-2 text-xs"></i>
                        All Content
                    </a>
                    
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-folder mr-2 text-xs"></i>
                        Categories
                    </a>
                    
                    @php
                        $contentCategories = \App\Models\Content\Category::where('published', 1)->orderBy('title')->get();
                    @endphp
                    
                    @if($contentCategories->count() > 0)
                        @foreach($contentCategories as $category)
                            <a href="{{ route('admin.content.index', ['category' => $category->id]) }}" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->get('category') == $category->id ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-chevron-right mr-2 text-xs"></i>
                                {{ $category->title }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- User info -->
    <div class="px-4 py-4 border-t border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    {{ auth('custom')->user()->username ?? 'Admin' }}
                </p>
                <p class="text-xs text-gray-400 truncate">Администратор</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center px-3 py-2 text-sm text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200">
                <i class="fas fa-sign-out-alt mr-3"></i>
                Logout
            </button>
        </form>
    </div>
</div>