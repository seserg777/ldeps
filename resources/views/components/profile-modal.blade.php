@props(['user'])

<x-modal name="profile-modal" title="Мій профіль" max-width="md">
    <div class="space-y-4">
        <!-- User info -->
        <div class="border-b border-gray-200 pb-4">
            <p class="text-sm text-gray-600 mb-1">Ім'я користувача</p>
            <p class="text-lg font-medium text-gray-900">{{ $user->username ?? $user->name }}</p>
            
            @if($user->email)
            <p class="text-sm text-gray-600 mt-2 mb-1">Електронна пошта</p>
            <p class="text-gray-900">{{ $user->email }}</p>
            @endif
        </div>

        <!-- Profile link -->
        <a
            href="{{ route('profile') }}"
            class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg text-center transition-colors duration-200"
        >
            Редагувати профіль
        </a>

        <!-- Orders link -->
        <a
            href="{{ route('profile.orders') }}"
            class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg text-center transition-colors duration-200"
        >
            Мої замовлення
        </a>

        <!-- Logout form -->
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200"
            >
                Вийти
            </button>
        </form>
    </div>
</x-modal>

