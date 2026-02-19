<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Planillas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Título + Logo -->
                <div class="flex items-center justify-center mb-6">
                    <!-- Título -->
                    <h2 class="text-2xl md:text-4xl font-bold text-black dark:text-gray-200 uppercase tracking-wide drop-shadow-lg mr-4 text-center">
                        DPTO. DE MANTENIMIENTO - ASESP
                    </h2>

                    <!-- Logo -->
                    <div class="shrink-0">
                        <x-application-logo class="h-16 w-auto" />
                    </div>
                </div>

                <!-- 3 COLUMNAS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                    <!-- Columna 1 -->
                    <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 dark:border-blue-300 p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                        <ul class="list-disc list-inside text-blue-800 dark:text-blue-200 space-y-2 text-sm font-semibold">
                            <li>F/SA = SIN AVISO</li>
                            <li>F/CA = CON AVISO</li>
                            <li>F/FF = FALLECIO FAMILIAR</li>
                            <li>F/FI = FAMILIAR INTERNADO</li>
                            <li>F/DIS = FAMILIAR.DISCAP.A CARGO</li>
                        </ul>
                    </div>

                    <!-- Columna 2 -->
                    <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-500 dark:border-red-300 p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                        <ul class="list-disc list-inside text-red-800 dark:text-red-200 space-y-2 text-sm font-semibold">
                            <li>6Y1 = LIBRE X 6Y1</li>
                            <li>LPHEX = LIBRE X HORAS EXTRAS</li>
                            <li>LXF = LIBRE X FERIADO</li>
                            <li>LDS = LIBRE X DONACIÓN DE SANGRE</li>
                            <li>LG = LIBRE GREMIAL</li>
                            <li>CH = CAMBIO DE HORARIO</li>
                            <li>CL = CAMBIO DE LIBRE</li>
                            <li>LP = LIBRE PARTICULAR</li>
                            <li>LPXC = LIBRE PARTICULAR X CONVENIO</li>
                            <li>ASRP = AUT.SALIDA X RAZONES PART.</li>
                            <li>ASHEX = AUT.SALIDA X HS.EXTRAS</li>
                        </ul>
                    </div>

                    <!-- Columna 3 -->
                    <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-500 dark:border-yellow-300 p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                        <ul class="list-disc list-inside text-yellow-800 dark:text-yellow-200 space-y-2 text-sm font-semibold">
                            <li>LIBRE X HS.EXTRAS = 24HS ANTES</li>
                            <li>LIBRE PARTICULAR = 72HS ANTES (2X MES Y 12X AÑO)</li>
                            <li>LIBRE X 6Y1 = 72HS ANTES (APROBACIÓN 28HS ANTES)</li>
                            <li>LIBRE DONACIÓN DE SANGRE = 48HS</li>
                            <li>CAMBIO DE HORARIO = 24HS ANTES</li>
                            <li>FALTA CON AVISO = 4HS ANTES</li>
                            <li>LXFERIADO = 24HS ANTES</li>
                            <li>LIBRE GREMIAL = 48HS ANTES</li>
                            <li>CAMBIO DE LIBRE = 48HS ANTES</li>
                            <li>LPXC = 48HS ANTES</li>
                            <li>ASRP = 24HS ANTES</li>
                            <li>ASHEX = 24HS ANTES</li>
                        </ul>
                    </div>

                </div>

                <div class="p-6 text-gray-900 dark:text-gray-200">
                    <h1 class="text-2xl font-bold text-center my-10"></h1>
                    <div class="md:flex md:justify-center p-5">
                        <livewire:crear-planilla />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
