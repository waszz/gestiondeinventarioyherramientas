<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        {{ __('¿Olvidaste tu Password? coloca tu email de registro y enviaremos en enlace para que puedas crear uno nuevo.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="dark:text-gray-300" />
            <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-between my-5">
            <x-link :href="route('login')" class="dark:text-gray-300">
                Iniciar Sesión
            </x-link>

            <x-link :href="route('register')" class="dark:text-gray-300">
                Crear Cuenta
            </x-link>
        </div>

        <x-primary-button class="w-full justify-center dark:bg-blue-600 dark:hover:bg-blue-500">
            {{ __('Enviar Instrucciones') }}
        </x-primary-button>
    </form>
</x-guest-layout>
