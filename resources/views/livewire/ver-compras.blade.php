<div wire:poll.2s class="max-w-7xl mx-auto p-6 bg-white dark:bg-gray-800 rounded shadow">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Compras</h2>

    @if(session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-center">
            {{ session('message') }}
        </div>
    @endif

    <!-- Input de búsqueda general -->
    <div class="mb-4">
        <input type="text" wire:model="search"
               placeholder="Buscar por funcionario, SO, descripción, estado o material..."
               class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-200">
    </div>

    <table class="min-w-full border border-gray-300 dark:border-gray-600 border-collapse text-center">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">Funcionario</th>
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">SO</th>
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">Concepto</th>
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">Estado</th>
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">Fecha SO</th>
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">Fecha Creación</th>
                <th class="px-6 py-3 border border-gray-300 dark:border-gray-500">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($compras as $compra)
                <!-- Fila principal -->
                <tr class="text-gray-800 dark:text-gray-200">
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">{{ $compra->user->name ?? '—' }}</td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">{{ $compra->SO }}</td>

                    <!-- Descripción con flechita -->
                   <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
    <div class="flex justify-between items-center border-none">
        <span>{{ $compra->descripcion }}</span>
        <svg wire:click="toggleExpanded({{ $compra->id }})"
             class="w-5 h-5 transform transition-transform duration-200 cursor-pointer {{ $expandedCompra === $compra->id ? 'rotate-90' : '' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </div>
</td>


                    <!-- Estado editable -->
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
                        <select wire:change="cambiarEstado({{ $compra->id }}, $event.target.value)"
                                class="border rounded px-2 py-1 pr-8 dark:bg-gray-700 dark:text-gray-200">
                            <option value="compras" {{ $compra->estado === 'compras' ? 'selected' : '' }}>Compras</option>
                            <option value="proveedor" {{ $compra->estado === 'proveedor' ? 'selected' : '' }}>Proveedor</option>
                            <option value="en stock" {{ $compra->estado === 'en stock' ? 'selected' : '' }}>En Stock</option>
                        </select>
                    </td>

                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">{{ $compra->fechaSO->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">{{ $compra->created_at->format('d/m/Y H:i') }}</td>
                   <td class="px-6 py-4 border border-gray-300 dark:border-gray-600">
    <div class="flex justify-center gap-2 border-none">
        @if(auth()->id() === $compra->user_id || auth()->user()->role === 'supervisor')
            <a href="{{ route('compras.editar', $compra->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">Editar</a>
            <div class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
             <button wire:click="$dispatch('confirmarEliminacion', { id: {{ $compra->id }} })">Eliminar</button>
            </div>
        @endif
    </div>
</td>

                </tr>

                <!-- Fila expandida: lista de materiales -->
                @if($expandedCompra === $compra->id)
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="7" class="px-6 py-3 text-left">
                            <strong>Lista de materiales:</strong>
                            @if($compra->lista_materiales)
                                <ul class="list-disc list-inside mt-1">
                                    @foreach(explode(',', $compra->lista_materiales) as $material)
                                        <li>{{ trim($material) }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500 dark:text-gray-300">Sin materiales</span>
                            @endif
                        </td>
                    </tr>
                @endif

            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 border border-gray-300 dark:border-gray-600 text-center text-gray-500 dark:text-gray-300">
                        No hay compras registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
     <div class="mt-4">
        {{ $compras->links() }}
    </div>
</div>

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Livewire.on('confirmarEliminacion', data => {
    Swal.fire({
        title: '¿Eliminar Compra?',
        text: "Una compra eliminada no se puede recuperar",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarCompra', data);
            Swal.fire('Eliminada', 'La compra se eliminó correctamente', 'success');
        }
    })
});


</script>
@endpush
