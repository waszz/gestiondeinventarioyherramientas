<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventario') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-200">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo centrado -->
        <div>
            <a href="{{ route('home') }}">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500 dark:text-gray-300" />
            </a>
        </div>

        <!-- Contenedor de contenido -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-700 shadow-md dark:shadow-gray-800 overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <!-- Script para modo oscuro -->
    <script>
        const toggleButton = document.getElementById('dark-mode-toggle');
        const icon = document.getElementById('dark-mode-icon');

        // Activar modo oscuro si ya estaba guardado
        if(localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
            if(icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
        }

        toggleButton?.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const darkActive = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', darkActive);
            if(icon) {
                icon.classList.toggle('fa-moon', !darkActive);
                icon.classList.toggle('fa-sun', darkActive);
            }
        });
    </script>

</body>
</html>
