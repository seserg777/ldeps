@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'user-menu ' . $class]) }} x-data>
    @auth
        <!-- Authorized user icon (without lock) -->
        <button
            type="button"
            @click.prevent="$dispatch('open-modal', 'profile-modal'); console.log('Profile modal clicked')"
            class="flex items-center justify-center p-2 text-gray-900 hover:text-green-600 transition-colors duration-200 rounded-lg hover:bg-gray-100"
            aria-label="Профіль користувача"
        >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </button>

        <!-- Profile modal -->
        <x-profile-modal :user="auth()->user()" />
    @else
        <!-- Unauthorized user icon (with lock) -->
        <button
            type="button"
            @click.prevent="$dispatch('open-modal', 'auth-modal'); console.log('Auth modal clicked')"
            class="flex items-center justify-center p-2 text-gray-900 hover:text-green-600 transition-colors duration-200 rounded-lg hover:bg-gray-100"
            aria-label="Увійти"
        >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <!-- User silhouette -->
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                <!-- Lock overlay (positioned at bottom right) -->
                <g transform="translate(15, 15)">
                    <rect x="0" y="2.5" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.8" fill="white"/>
                    <path d="M1.5 2.5V1.5a2 2 0 0 1 4 0v1" stroke="currentColor" stroke-width="1.8" fill="none"/>
                </g>
            </svg>
        </button>

        <!-- Auth modal -->
        <x-auth-modal />
    @endauth
</div>

