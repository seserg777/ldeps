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
                Панель
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                Дашборд
            </a>
        </div>

        <!-- Catalog -->
        <div class="space-y-1 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Каталог
            </div>
            <a href="{{ route('admin.products') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.products*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-box mr-3 text-gray-400"></i>
                Товары
            </a>
            <a href="{{ route('admin.categories') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.categories*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-tags mr-3 text-gray-400"></i>
                Категории
            </a>
            <a href="{{ route('admin.manufacturers') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.manufacturers*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-industry mr-3 text-gray-400"></i>
                Производители
            </a>
        </div>

        <!-- Sales -->
        <div class="space-y-1 mt-6">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 py-2">
                Продажи
            </div>
            <a href="{{ route('admin.orders') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.orders*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-shopping-cart mr-3 text-gray-400"></i>
                Заказы
            </a>
            <a href="{{ route('admin.customers') }}" 
               class="flex items-center px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-800 hover:text-white transition-colors duration-200 {{ request()->routeIs('admin.customers*') ? 'bg-gray-800 text-white' : '' }}">
                <i class="fas fa-users mr-3 text-gray-400"></i>
                Клиенты
            </a>
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
                Выйти
            </button>
        </form>
    </div>
</div>