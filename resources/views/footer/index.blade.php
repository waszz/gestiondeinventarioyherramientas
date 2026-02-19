<footer class="bg-gray-900 text-gray-200 py-2 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Logo -->
        <div class="flex flex-col md:flex-row items-center md:justify-between text-center md:text-left gap-2 mb-2">
            <p class="text-gray-400 text-xs max-w-sm">
                Desarrollado por MasDigital.
            </p>
            <a href="https://masdigital.com.uy/" target="_blank">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto">
            </a>
        </div>

        <!-- Derechos -->
        <div class="border-t border-gray-700 pt-3 text-center text-xs text-gray-500">
            © {{ date('Y') }} Todos los derechos reservados.
        </div>
    </div>
</footer>