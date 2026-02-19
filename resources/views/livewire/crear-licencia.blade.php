<div class="max-w-md mx-auto mt-10 p-6 bg-white dark:bg-gray-800 rounded shadow">

    <!-- Mensajes arriba -->
    @if(session()->has('message'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded text-center">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error_funcionario'))
        <div class="mb-4 p-2 bg-red-100 text-red-800 rounded text-center">
            {{ session('error_funcionario') }}
        </div>
    @endif

    <h3 class="font-semibold mb-4 text-center text-lg">Crear Licencia</h3>

    <div class="space-y-3">
        <!-- Número de funcionario -->
        <input type="text" placeholder="Número de Funcionario" wire:model="numero_funcionario"
               class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white">
        @error('numero_funcionario') 
            <span class="text-red-600 text-sm">{{ $message }}</span> 
        @enderror

        <button wire:click="generarDatos" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
            Generar
        </button>

        <!-- Campos generados automáticamente -->
        @if($nombre)
            <input type="text" wire:model="nombre" readonly
                   class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Nombre">

            <input type="text" wire:model="apellido" readonly
                   class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Apellido">

            <input type="text" wire:model="empresa" readonly
                   class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Empresa">

            <input type="text" wire:model="area" readonly
                   class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Área">

            <input type="text" wire:model="turno" readonly
                   class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Turno">
        @endif

        <label class="block">Fecha Inicio</label>
        <input type="date" wire:model="fecha_inicio" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white">

        <label class="block">Fecha Fin</label>
        <input type="date" wire:model="fecha_fin" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white">

        <div class="flex justify-between items-center">
            <div class="w-1/2 mr-1">
                <label class="block text-sm">Días (solo referencia)</label>
                <input type="number" min="1" wire:model="cantidad_dias"
                       class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white">
            </div>
            <div class="w-1/2 ml-1">
                <label class="block text-sm">Días restantes (solo referencia)</label>
                <input type="number" min="0" wire:model="dias_restantes"
                       class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white">
            </div>
        </div>

        <!-- <div class="flex items-center space-x-2 mt-2">
            <input type="checkbox" wire:model="presentismo" id="presentismo" class="rounded">
            <label for="presentismo">Presentismo</label>
        </div> -->

        <button wire:click="guardarLicencia" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-3">
            Crear Licencia
        </button>
    </div>
</div>

@push('scripts')
<script>
    Livewire.on('licenciaCreada', () => {
        // Aquí podrías agregar animación o scroll al mensaje
    });
</script>
@endpush
