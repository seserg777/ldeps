@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'user-menu ' . $class]) }}>
    @auth
        <!-- Authorized user icon (without lock) -->
        <button
            @click="$dispatch('open-modal', 'profile-modal')"
            class="flex items-center justify-center p-2 text-gray-700 hover:text-green-600 transition-colors duration-200"
            aria-label="Профіль користувача"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </button>

        <!-- Profile modal -->
        <x-profile-modal :user="auth()->user()" />
    @else
        <!-- Unauthorized user icon (with lock) -->
        <button
            @click="$dispatch('open-modal', 'auth-modal')"
            class="flex items-center justify-center p-2 text-gray-700 hover:text-green-600 transition-colors duration-200"
            aria-label="Увійти"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <!-- User silhouette -->
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                <!-- Lock overlay (positioned at bottom right) -->
                <g transform="translate(14, 14)">
                    <rect x="0" y="3" width="6" height="5" rx="1" stroke="currentColor" stroke-width="1.5" fill="white"/>
                    <path d="M1.5 3V2a1.5 1.5 0 0 1 3 0v1" stroke="currentColor" stroke-width="1.5" fill="none"/>
                </g>
            </svg>
        </button>

        <!-- Auth modal -->
        <x-auth-modal />
    @endauth
</div>

