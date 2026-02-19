<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Planilla') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Título + Logo -->
                <div class="flex items-center justify-center mb-6">
                    <h2 class="text-2xl md:text-4xl font-bold text-black dark:text-gray-200 uppercase tracking-wide drop-shadow-lg mr-4 text-center">
                        DPTO. DE MANTENIMIENTO - ASESP
                    </h2>

                    <div class="shrink-0">
                        <x-application-logo class="h-16 w-auto" />
                    </div>
                </div>

                <!-- 3 COLUMNAS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                    <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 dark:border-blue-300 p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                        <ul class="list-disc list-inside text-blue-800 dark:text-blue-200 space-y-2 text-sm font-semibold">
                            <li>F/SA = SIN AVISO</li>
                            <li>F/CA = CON AVISO</li>
                            <li>F/FF = FALLECIO FAMILIAR</li>
                            <li>F/FI = FAMILIAR INTERNADO</li>
                            <li>F/DIS = FAMILIAR.DISCAP.A CARGO</li>
                        </ul>
                    </div>

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

                <!-- Formulario Editar Planilla -->
                <div class="p-6 text-gray-900 dark:text-gray-200">
                    <div class="md:flex md:justify-center p-5">
                        <div class="max-w-xl mx-auto bg-white dark:bg-gray-700 shadow-lg p-6 rounded-lg w-full">
                            <h2 class="text-2xl font-semibold mb-6 text-center text-black dark:text-gray-200">Editar Planilla</h2>

                            <form wire:submit.prevent="actualizarPlanilla" class="space-y-4">
                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Horario habitual</label>
                                    <input type="text" wire:model="horario_habitual" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                    @error('horario_habitual') <span class="text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Número funcionario</label>
                                    <input type="text" wire:model="numero_funcionario" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                    @error('numero_funcionario') <span class="text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-700 dark:text-gray-200">Nombre</label>
                                        <input type="text" wire:model="nombre" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                        @error('nombre') <span class="text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-700 dark:text-gray-200">Apellido</label>
                                        <input type="text" wire:model="apellido" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                        @error('apellido') <span class="text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                 <!-- Empresa -->
        <div>
            <label for="empresa" class="block font-semibold text-gray-700 dark:text-gray-200">Empresa</label>
            <select id="empresa" wire:model="empresa"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                <option value="">-- Seleccione una empresa --</option>
                <option value="Gente">Gente</option>
                <option value="Prosepri">Prosepri</option>
                <option value="Asoc. Española">Asoc. Española</option>
            </select>
            @error('empresa') 
                <span class="text-red-600">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Cargo -->
<div>
    <label for="cargo" class="block font-semibold text-gray-700 dark:text-gray-200">Cargo</label>
    <select id="cargo" wire:model="cargo"
            class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
        <option value="">-- Seleccione un cargo --</option>
        <option value="Peón">Peón</option>
        <option value="Suboficial">Suboficial</option>
        <option value="Oficial">Oficial</option>
        <option value="Especializado">Especializado</option>
    </select>
    @error('cargo') 
        <span class="text-red-600">{{ $message }}</span> 
    @enderror
</div>

<!-- Área -->
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Área</label>
    <select wire:model="area" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
        <option value="">Seleccione un área</option>
        <option value="Reparaciones Generales">Rep. Grales</option>
        <option value="Electricidad">Electricidad</option>
        <option value="Aire Acondicionado">A/A</option>
        <option value="Pintura">Pintura</option>
        <option value="Administrativa">Administrativo</option>
    </select>
    @error('area') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
</div>

                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Registro de faltas</label>
                                    <textarea wire:model="registro_faltas" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100"></textarea>
                                    @error('registro_faltas') <span class="text-red-600">{{ $message }}</span> @enderror
                                </div>

                                  <!-- Solicita -->
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-200">Solicita</label>
            <select wire:model="solicita" 
                    class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                <option value="">-- Seleccione una opción --</option>
                <option value="F/SA = SIN AVISO">F/SA = SIN AVISO</option>
                <option value="F/CA = CON AVISO">F/CA = CON AVISO</option>
                <option value="F/FF = FALLECIO FAMILIAR">F/FF = FALLECIO FAMILIAR</option>
                <option value="F/FI = FAMILIAR INTERNADO">F/FI = FAMILIAR INTERNADO</option>
                <option value="F/DIS = FAMILIAR.DISCAP.A CARGO">F/DIS = FAMILIAR.DISCAP.A CARGO</option>
                <option value="6Y1 = LIBRE X 6Y1">6Y1 = LIBRE X 6Y1</option>
                <option value="LPHEX = LIBRE X HORAS EXTRAS">LPHEX = LIBRE X HORAS EXTRAS</option>
                <option value="LXF = LIBRE X FERIADO">LXF = LIBRE X FERIADO</option>
                <option value="LDS = LIBRE X DONACIÓN DE SANGRE">LDS = LIBRE X DONACIÓN DE SANGRE</option>
                <option value="LG = LIBRE GREMIAL">LG = LIBRE GREMIAL</option>
                <option value="CH = CAMBIO DE HORARIO">CH = CAMBIO DE HORARIO</option>
                <option value="CL = CAMBIO DE LIBRE">CL = CAMBIO DE LIBRE</option>
                <option value="LP = LIBRE PARTICULAR">LP = LIBRE PARTICULAR</option>
                <option value="LPXC = LIBRE PARTICULAR X CONVENIO">LPXC = LIBRE PARTICULAR X CONVENIO</option>
                <option value="ASRP = AUT.SALIDA X RAZONES PART.">ASRP = AUT.SALIDA X RAZONES PART.</option>
                <option value="ASHEX = AUT.SALIDA X HS.EXTRAS">ASHEX = AUT.SALIDA X HS.EXTRAS</option>
            </select>
            @error('solicita') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-700 dark:text-gray-200">De la Fecha</label>
                                        <input type="date" wire:model="fecha_inicio" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                        @error('fecha_inicio') <span class="text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-700 dark:text-gray-200">A la Fecha</label>
                                        <input type="date" wire:model="fecha_fin" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                        @error('fecha_fin') <span class="text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-700 dark:text-gray-200">Horario a realizar</label>
                                        <input type="text" wire:model="horario_a_realizar" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                        @error('horario_a_realizar') <span class="text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-700 dark:text-gray-200">Motivos</label>
                                        <input type="text" wire:model="motivos" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                        @error('motivos') <span class="text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Firma</label>
                                    <input type="text" wire:model="autoriza" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md shadow-sm dark:text-gray-100">
                                    @error('autoriza') <span class="text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex justify-center">
                                    <x-primary-button type="submit" class="w-3/4 sm:w-1/2 justify-center bg-green-600 hover:bg-green-700">
                                        {{ __('Actualizar Planilla') }}
                                    </x-primary-button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
