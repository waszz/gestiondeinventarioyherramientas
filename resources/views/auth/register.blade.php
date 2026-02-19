<x-guest-layout>
    <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-gray-100 mb-6">
        {{ __('Crear Cuenta') }}
    </h2>

    <form method="POST" action="{{ route('register') }}" novalidate class="space-y-4">
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Repetir Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Links -->
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
            <x-link :href="route('password.request')">
                {{ __('Olvidaste tu Password') }}
            </x-link>

            <x-link :href="route('login')">
                {{ __('Iniciar Sesión') }}
            </x-link>
        </div>

        <!-- Submit -->
        <x-primary-button class="w-full justify-center bg-[#04a6e7] hover:bg-[#069dda] mt-2">
            {{ __('Registrarse') }}
        </x-primary-button>
    </form>
</x-guest-layout>
