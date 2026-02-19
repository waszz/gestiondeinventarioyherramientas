<x-app-layout>
@auth
  <!-- Bienvenida con GSAP -->
<div id="bienvenida" class="flex flex-col items-center justify-center mt-20 text-center">
    <!-- Título -->
    <h1 class="text-5xl font-extrabold text-gray-800 dark:text-gray-200 mb-6 tracking-wide">
        ¡Bienvenido, {{ Auth::user()->name }}!
    </h1>

    <!-- Logo desde asset -->
    <div class="mb-8">
        <img id="logo" src="{{ asset('images/española.png') }}" 
             alt="Logo" class="h-56 w-auto mx-auto drop-shadow-lg" />
    </div>

    <!-- Botón solo para admin o supervisor -->
    @if(in_array(Auth::user()->role, ['admin', 'supervisor']))
        <a href="{{ route('panol.materiales') }}"
           id="btn-entrar"
           class="px-6 py-3 text-lg font-semibold text-white rounded-xl shadow-lg 
                  bg-[#04a6e7] hover:bg-[#069dda] transition">
            Entrar al Sistema de Inventario
        </a>
    @endif
</div>
@else
        <!-- Formulario de Login (lo que ya tenías) -->
        <div class="flex justify-center mt-10">
            <a href="{{ route('home') }}">
                <x-application-logo class="block h-16 w-auto fill-current text-gray-800 dark:text-gray-200" />
            </a>
        </div>

        <div class="max-w-md mx-auto mt-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md dark:shadow-gray-700">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="dark:text-gray-200" />
                    <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                  type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 dark:text-red-400" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="dark:text-gray-200" />
                    <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                                  type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 dark:text-red-400" />
                </div>

                <!-- Recordarme -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center dark:text-gray-200">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                               name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Recordarme') }}</span>
                    </label>
                </div>

                <!-- Links -->
                <div class="flex justify-between my-5">
                    <x-link :href="route('password.request')" class="dark:text-gray-300 dark:hover:text-white">
                        Olvidaste tu Password
                    </x-link>

                    <x-link :href="route('register')" class="dark:text-gray-300 dark:hover:text-white">
                        Crear Cuenta
                    </x-link>
                </div>

                <!-- Botón Ingresar -->
                <x-primary-button
                    class="w-full justify-center bg-[#04a6e7] hover:bg-[#069dda] dark:bg-blue-600 dark:hover:bg-blue-500 dark:text-white">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </form>
        </div>
    @endauth


<!-- Script GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (document.getElementById("bienvenida")) {
            // Animación del título
            gsap.from("#bienvenida h1", { 
                duration: 1, 
                y: -80, 
                opacity: 0, 
                ease: "power3.out" 
            });

            // Animación del logo PNG
            gsap.from("#logo", { 
                duration: 1.2, 
                scale: 0, 
                opacity: 0, 
                delay: 0.5, 
                ease: "elastic.out(1, 0.5)" 
            });

            // Animación del botón
            gsap.fromTo("#btn-entrar",
                { y: 40, opacity: 0 },
                { y: 0, opacity: 1, duration: 1, ease: "power2.out", delay: 1 }
            );
        }
    });
</script>
</x-app-layout>
