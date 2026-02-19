<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="dark:text-gray-300" />
            <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="dark:text-gray-300" />
            <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200"
                type="password"
                name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center dark:text-gray-300">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex justify-between my-5">
            <x-link :href="route('password.request')" class="dark:text-gray-300">
                Olvidaste tu Password
            </x-link>

            <x-link :href="route('register')" class="dark:text-gray-300">
                Crear Cuenta
            </x-link>
        </div>

        <x-primary-button class="w-full justify-center dark:bg-blue-600 dark:hover:bg-blue-500">
            {{ __('Iniciar Sesión') }}
        </x-primary-button>
    </form> 
</x-guest-layout>
