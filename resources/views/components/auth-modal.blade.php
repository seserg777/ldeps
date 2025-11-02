<x-modal name="auth-modal" title="Увійти в акаунт" max-width="md">
    <form method="POST" action="{{ route('auth.login') }}" x-data="{ showPassword: false }">
        @csrf
        
        <!-- Hidden field to redirect back to current page after login -->
        <input type="hidden" name="redirect" value="{{ url()->current() }}">

        <!-- Username/Email -->
        <div class="mb-4">
            <label for="login-username" class="block text-sm font-medium text-gray-700 mb-1">
                Електронна пошта або логін
            </label>
            <input
                type="text"
                id="login-username"
                name="username"
                required
                autofocus
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                placeholder="dev-out"
            >
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">
                Пароль
            </label>
            <div class="relative">
                <input
                    :type="showPassword ? 'text' : 'password'"
                    id="login-password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent pr-10"
                    placeholder="••••••••••••"
                >
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                >
                    <!-- Eye icon -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <!-- Eye slash icon -->
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Remember me & Forgot password -->
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-600">Запам'ятати мене</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700">
                Забули свій пароль?
            </a>
        </div>

        <!-- Login button -->
        <button
            type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200"
        >
            Увійти
        </button>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Новий покупець?</span>
            </div>
        </div>

        <!-- Register button -->
        <a
            href="{{ route('register') }}"
            class="block w-full bg-gray-100 hover:bg-gray-200 text-green-600 font-medium py-3 px-4 rounded-lg text-center transition-colors duration-200"
        >
            Зареєструватися
        </a>
    </form>
</x-modal>

