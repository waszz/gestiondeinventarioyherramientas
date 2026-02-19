<nav x-data="{ open: false }" class="bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700">
    <!-- Contenedor principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo y menús principales -->
            <div class="flex items-center space-x-8">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('posts.index') }}">
                        <x-application-logo class="block h-6 sm:h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Menús principales visibles en pantalla grande -->
                <div class="hidden sm:flex space-x-6 items-center">
                    @auth
                        @php
                            $user = auth()->user();
                        @endphp

                        {{-- Admin y Supervisor --}}
                        @if($user && ($user->role === 'admin' || $user->role === 'supervisor'))
                            <!-- Administración -->
                            <div x-data="{ openAdmin: false }" class="relative">
                                <x-nav-link href="#" @click.prevent="openAdmin = !openAdmin" 
                                    class="cursor-pointer flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">
                                    Administración
                                    <svg :class="{'rotate-180': openAdmin}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </x-nav-link>

                                <div x-show="openAdmin" @click.outside="openAdmin = false" x-transition
                                     class="absolute mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow z-50">
                                    {{-- <a href="{{ route('posts.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Planillas</a>
                                    <a href="{{ route('posts.create') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Crear Planillas</a>
                                    <a href="{{ route('planillas.generar') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Generar Planilla</a> --}}
                                    <a href="{{ route('funcionarios.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Funcionarios</a>
                                    <a href="{{ route('reportes.index') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Reportes</a>
                                </div>
                            </div>

                            <!-- Compras -->
                            <div x-data="{ openCompras: false }" class="relative">
                                <x-nav-link href="#" @click.prevent="openCompras = !openCompras"
                                    class="cursor-pointer flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">
                                    Compras
                                    <svg :class="{'rotate-180': openCompras}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </x-nav-link>

                                <div x-show="openCompras" @click.outside="openCompras = false" x-transition
                                     class="absolute mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow z-50">
                                     <a href="{{ route('panol.pedidos') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Ver Pedidos</a>

                                    
                                </div>
                            </div>
                                                <!-- Licencias -->
<div x-data="{ openLicencias: false }" class="relative">
    <x-nav-link href="#" @click.prevent="openLicencias = !openLicencias"
        class="cursor-pointer flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white">
        CIH
        <svg :class="{'rotate-180': openLicencias}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </x-nav-link>

    <div x-show="openLicencias" @click.outside="openLicencias = false" x-transition
         class="absolute mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow z-50">
         <a href="{{ route('panol.materiales') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Materiales</a>
         <a href="{{ route('panol.herramientas') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">Herramientas</a>
    </div>
</div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Usuario y hamburguesa -->
            <div class="flex items-center space-x-4">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">
                                {{ Auth::user()->name }}
                                <svg class="ml-2 w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Cerrar Sesión
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-nav-link :href="route('login')" class="dark:text-gray-200">Iniciar Sesión</x-nav-link>
                    <x-nav-link :href="route('register')" class="dark:text-gray-200">Crear Cuenta</x-nav-link>
                @endauth

                <!-- Botón hamburguesa -->
                <div class="sm:hidden">
                    <button @click="open = !open" class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Menú responsive -->
    <div :class="{'block': open, 'hidden': !open}" class="sm:hidden bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @php $user = auth()->user(); @endphp
                {{-- Admin y Supervisor --}}
                @if($user && ($user->role === 'admin' || $user->role === 'supervisor'))
                    <!-- Administración Mobile -->
                    <div x-data="{ openAdminMobile: false }" class="px-4">
                        <button @click="openAdminMobile = !openAdminMobile" class="w-full text-left text-gray-700 dark:text-gray-200 py-2 flex justify-between items-center">
                            Administración
                            <svg :class="{ 'rotate-180': openAdminMobile }" class="w-4 h-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openAdminMobile" class="pl-4">
                            <x-responsive-nav-link :href="route('posts.index')" class="dark:text-gray-200">Planillas</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('posts.create')" class="dark:text-gray-200">Crear Planillas</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('planillas.generar')" class="dark:text-gray-200">Generar Planilla</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('funcionarios.index')" class="dark:text-gray-200">Funcionarios</x-responsive-nav-link>
                        </div>
                    </div>

                    <!-- Compras Mobile -->
                    <div x-data="{ openComprasMobile: false }" class="px-4 mt-1">
                        <button @click="openComprasMobile = !openComprasMobile" class="w-full text-left text-gray-700 dark:text-gray-200 py-2 flex justify-between items-center">
                            Compras
                            <svg :class="{ 'rotate-180': openComprasMobile }" class="w-4 h-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openComprasMobile" class="pl-4">
                         <x-responsive-nav-link :href="route('compras.create')" class="dark:text-gray-200">Crear Compras</x-responsive-nav-link>
                         <x-responsive-nav-link :href="route('compras.index')" class="dark:text-gray-200">Ver Compras</x-responsive-nav-link>
                         
                        </div>
                    </div>

                @endif

                {{-- Usuario info --}}
                <div class="border-gray-200 dark:border-gray-700 mt-4 pt-4 px-4">
                    <div class="text-gray-800 dark:text-gray-200 font-semibold">{{ Auth::user()->name }}</div>
                    <div class="text-gray-500 dark:text-gray-400 text-sm">{{ Auth::user()->email }}</div>
                </div>
                <x-responsive-nav-link :href="route('profile.edit')" class="dark:text-gray-200">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="dark:text-gray-200">Cerrar Sesión</x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link :href="route('login')" class="dark:text-gray-200">Iniciar Sesión</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" class="dark:text-gray-200">Crear Cuenta</x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>
