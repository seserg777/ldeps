<div class="flex items-center justify-between px-6 py-4">
    <!-- Page title -->
    <div class="flex items-center space-x-4">
        <h1 class="text-xl font-semibold text-gray-900">
            @yield('title', 'Dashboard')
        </h1>
        @hasSection('breadcrumbs')
            <nav class="hidden md:flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    @yield('breadcrumbs')
                </ol>
            </nav>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex items-center space-x-4">
        <!-- Search -->
        <div class="hidden md:block">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       placeholder="Search..." 
                       class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center space-x-2">
            @hasSection('actions')
                @yield('actions')
            @else
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Create
                </button>
            @endif

            <!-- Go to site -->
            <a href="/" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
               title="Go to site">
                <i class="fas fa-external-link-alt mr-2"></i>
                Go to Site
            </a>
        </div>
    </div>
</div>