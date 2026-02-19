<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 shadow-lg p-6 rounded-lg">
    <h2 class="text-2xl font-semibold mb-6 text-center text-black dark:text-gray-200">Planilla de Registro Diario</h2>

    <form wire:submit.prevent="guardarPlanilla" class="space-y-4">

        <!-- Horario habitual -->
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-200">Horario habitual</label>
            <input type="text" wire:model="horario_habitual"
                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
            @error('horario_habitual') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>

<!-- Número funcionario con autocomplete -->
<!-- Número funcionario -->
<div class="relative">
    <label class="block font-semibold text-gray-700 dark:text-gray-200">
        Número funcionario
    </label>

    <input type="text" wire:model="numero_funcionario" 
           placeholder="Escriba el número de funcionario"
           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-200 rounded-md shadow-sm">

    <!-- Mostrar error desde la propiedad -->
    @if($errorNumeroFuncionario)
        <p class="mt-1 text-sm text-red-600">
            {{ $errorNumeroFuncionario }}
        </p>
    @endif

    <!-- Lista de sugerencias -->
    @if($mostrarSugerencias && !empty($funcionariosSugeridos))
        <ul class="absolute z-10 w-full bg-white border border-gray-300 dark:bg-gray-700 
                   dark:border-gray-600 rounded-md mt-1 max-h-48 overflow-auto">
            @foreach($funcionariosSugeridos as $func)
                <li wire:click="seleccionarFuncionario({{ $func->id }})"
                    class="px-3 py-2 hover:bg-blue-500 hover:text-white cursor-pointer">
                    {{ $func->numero_funcionario }} - {{ $func->nombre }} {{ $func->apellido }}
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Botón Generar Inputs -->
    <button type="button"
            wire:click="generarInputs"
            class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        Generar
    </button>
</div>




        <!-- Mostrar inputs solo si se generaron -->
        @if($inputsGenerados)
            <!-- Nombre y Apellido -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Nombre</label>
                    <input type="text" wire:model="nombre"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm" readonly>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Apellido</label>
                    <input type="text" wire:model="apellido"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm" readonly>
                </div>
            </div>

            <!-- Empresa y Cargo -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Empresa</label>
                    <input type="text" wire:model="empresa"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm" readonly>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 dark:text-gray-200">Cargo</label>
                    <input type="text" wire:model="cargo"
                           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm" readonly>
                </div>
            </div>
            <!-- Área -->
<div class="mt-4">
    <label class="block font-semibold text-gray-700 dark:text-gray-200">Área</label>
    <input type="text" wire:model="area"
           class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-200 rounded-md shadow-sm" readonly>
    @error('area') <span class="text-red-600">{{ $message }}</span> @enderror
</div>
        @endif

        <!-- Registro de faltas -->
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-200">Registro de faltas</label>
            <textarea wire:model="registro_faltas"
                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm"></textarea>
            @error('registro_faltas') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>

        <!-- Solicita -->
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-200">Solicita</label>
            <select wire:model="solicita"
                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
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

        <!-- Fechas -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-200">De la Fecha</label>
                <input type="date" wire:model="fecha_inicio"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                @error('fecha_inicio') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-200">A la Fecha</label>
                <input type="date" wire:model="fecha_fin"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                @error('fecha_fin') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Motivos y Horario a realizar -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-200">Horario a realizar</label>
                <input type="text" wire:model="horario_a_realizar"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                @error('horario_a_realizar') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-semibold text-gray-700 dark:text-gray-200">Motivos</label>
                <input type="text" wire:model="motivos"
                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                @error('motivos') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Autoriza -->
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-200">Firma</label>
            <input type="text" wire:model="autoriza" placeholder="Nombre del Jefe Operativo, Jefe, Subjefe"
                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 rounded-md shadow-sm">
            @error('autoriza') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>

        <!-- Botón Enviar -->
        <div class="flex flex-col items-center">
            <x-primary-button
                class="w-3/4 sm:w-1/2 justify-center bg-[#04a6e7] hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-800"
                wire:loading.attr="disabled" wire:target="guardarPlanilla">
                {{ __('Enviar Solicitud') }}
            </x-primary-button>

            <p wire:loading wire:target="guardarPlanilla" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                {{ __('Enviando...') }}
            </p>
        </div>

    </form>
</div>