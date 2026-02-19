<div class="max-w-md mx-auto mt-10 p-6 bg-white dark:bg-gray-800 rounded shadow">

    <!-- Mensajes arriba -->
    @if(session()->has('message'))
        <div class="mb-1 p-2 bg-green-100 text-green-800 rounded text-center">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error_funcionario'))
        <div class="mb-1 p-2 bg-red-100 text-red-800 rounded text-center">
            {{ session('error_funcionario') }}
        </div>
    @endif

    <form wire:submit.prevent="actualizarLicencia" class="space-y-4">

        <!-- Número de funcionario -->
        <input type="text" wire:model="numero_funcionario" placeholder="Número Funcionario"
               class="border p-2 rounded w-full bg-gray-200 dark:bg-gray-700 dark:text-white" readonly>

        <!-- Nombre + Apellido juntos -->
        <input type="text" value="{{ $nombre }} {{ $apellido }}" placeholder="Nombre y Apellido"
               class="border p-2 rounded w-full bg-gray-200 dark:bg-gray-700 dark:text-white" readonly>

        <!-- Empresa -->
        <input type="text" wire:model="empresa" placeholder="Empresa"
               class="border p-2 rounded w-full bg-gray-200 dark:bg-gray-700 dark:text-white" readonly>

        <!-- Área -->
        <input type="text" wire:model="area" placeholder="Área"
               class="border p-2 rounded w-full bg-gray-200 dark:bg-gray-700 dark:text-white" readonly>

        <!-- Turno -->
        <input type="text" wire:model="turno" placeholder="Turno"
               class="border p-2 rounded w-full bg-gray-200 dark:bg-gray-700 dark:text-white" readonly>

        <!-- Fechas inicio y fin juntos -->
        <div class="flex gap-2">
            <input type="date" wire:model="fecha_inicio"
                   class="border p-2 rounded w-1/2 bg-white dark:bg-gray-800 dark:text-white">
            <input type="date" wire:model="fecha_fin"
                   class="border p-2 rounded w-1/2 bg-white dark:bg-gray-800 dark:text-white">
        </div>

        <!-- Presentismo + Días + Días Restantes
        <div class="flex gap-2 items-center">
            <label class="flex items-center gap-2 text-gray-800 dark:text-white">
                <input type="checkbox" wire:model="presentismo" disabled class="accent-blue-500">
                Presentismo
            </label>
        </div> -->

        <!-- Estado editable -->
        <div>
            <label class="block text-gray-800 dark:text-white mb-1">Estado</label>
            <select wire:model="estado" 
                    class="border p-2 rounded w-full bg-white dark:bg-gray-800 dark:text-white">
                <option value="pendiente">Pendiente</option>
                <option value="autorizado">Autorizado</option>
                <option value="no autorizado">No autorizado</option>
            </select>
        </div>

        <!-- Botón actualizar -->
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-3">
            Actualizar Licencia
        </button>

    </form>
</div>
