<div class="max-w-md mx-auto p-6 bg-white dark:bg-gray-800 rounded shadow">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Editar Compra</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="actualizarCompra" class="space-y-4">
        <div>
            <label class="block text-gray-700 dark:text-gray-200">SO</label>
            <input type="text" wire:model="SO" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-200">
            @error('SO') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-gray-700 dark:text-gray-200">Concepto</label>
            <input type="text" wire:model="descripcion" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-200">
            @error('descripcion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

<!-- Lista de materiales -->
<div class="mb-4">
    <label class="block text-gray-700 dark:text-gray-200">Lista de materiales (separados por comas)</label>
    <textarea wire:model="lista_materiales"
              class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-200"
              rows="3"
              placeholder="Ej: Tornillos, Tuercas, Clavos"></textarea>
    @error('lista_materiales') 
        <span class="text-red-600 text-sm">{{ $message }}</span> 
    @enderror
</div>


        <div>
            <label class="block text-gray-700 dark:text-gray-200">Estado</label>
            <select wire:model="estado" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-200">
                <option value="compras">Compras</option>
                <option value="proveedor">Proveedor</option>
                <option value="en stock">En Stock</option>
            </select>
            @error('estado') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-gray-700 dark:text-gray-200">Fecha SO</label>
            <input type="date" wire:model="fechaSO" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-200">
            @error('fechaSO') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Actualizar Compra
            </button>
        </div>
    </form>
</div>
