<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-200">
    <div class="min-h-screen bg-white dark:bg-gray-800">
        <!-- Header con navegación y botón modo oscuro -->
        <header class="dark:bg-gray-900 shadow dark:shadow-gray-700">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center">
                <div class="flex items-center w-full justify-between">
                    <div class="flex-grow">
                        @include('layouts.navigation')
                    </div>
                    <button id="dark-mode-toggle" class="p-2 ml-4 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-yellow-300 shadow-lg transition">
                        <i id="dark-mode-icon" class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Secciones de usuario -->
        <div class="relative">
            <div class="absolute bottom-4 right-6 sm:right-4 z-50 mr-24">
                @yield('usuario-con-campana')
            </div>
        </div>
        <div class="absolute top-4 right-14 z-50 block sm:hidden">
            @yield('campana-movil')
        </div>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow dark:bg-gray-900 dark:shadow-gray-700 mt-4">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="px-4 sm:px-6 lg:px-8 mt-6">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts


    @stack('scripts')

    <!-- Flechita para volver arriba -->
    <button onclick="scrollToTop()" 
        class="fixed bottom-6 right-6 bg-gradient-to-r from-blue-500 via-red-500 to-yellow-400 
               text-white p-3 rounded-full shadow-lg transition duration-300 z-50 block sm:hidden"
        title="Volver arriba">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </button>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Modo oscuro
        const toggleButton = document.getElementById('dark-mode-toggle');
        const icon = document.getElementById('dark-mode-icon');

        if(localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }

        toggleButton.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            if(document.documentElement.classList.contains('dark')) {
                localStorage.setItem('darkMode', 'true');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                localStorage.setItem('darkMode', 'false');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    </script>

</body>
@include('footer.index')
</html>
