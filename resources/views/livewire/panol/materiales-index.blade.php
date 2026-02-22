
<div class="p-6">

    <h2 class="text-2xl font-bold mb-6">Gestión de Materiales</h2>
@if($hayStockBajo)
    <div class="fixed top-4 right-4 bg-red-600 text-white px-3 py-2 text-sm rounded shadow font-semibold z-50 w-auto max-w-xs">
           ⚠️ ALERTA: Hay materiales con STOCK BAJO.
        Revisar inventario y generar pedido.
    </div>
@endif

       {{-- ALERTA EXITO --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    {{-- ALERTA ELIMINACION --}}
    @if($materialAEliminar)
        <div class="mb-4 p-4 bg-red-100 border border-red-300 rounded shadow">
            <p class="font-bold text-red-700 mb-2">
                ¿Eliminar material?
            </p>
            <p class="mb-4">
                Estás por eliminar <strong>{{ $nombreMaterial }}</strong>
            </p>

            <div class="flex gap-3">
                <button wire:click="eliminarMaterial"
                    class="bg-red-600 text-white px-4 py-2 rounded">
                    Sí, eliminar
                </button>

                <button wire:click="cancelarEliminacion"
                    class="bg-gray-400 text-white px-4 py-2 rounded">
                    Cancelar
                </button>
            </div>
        </div>
    @endif

    {{-- Botón añadir material --}}
    <a href="{{ route('panol.materiales.crear') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
        + Añadir Nuevo material
    </a>
     <button wire:click="exportarCsv"
        class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 mb-4 rounded-lg">
    Exportar Materiales a CSV
</button>

<a href="{{ route('materiales.exportar.pdf') }}" class="btn btn-danger bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
    Exportar Materiales a PDF
</a>

<div class="flex items-center gap-2">
    <input type="file" wire:model="archivoImportacion" 
           class="border p-2 rounded">

    <button wire:click="importarMateriales"
            class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Importar Materiales desde Excel
    </button>
</div>

@error('archivoImportacion') 
    <span class="text-red-500">{{ $message }}</span> 
@enderror

    {{-- Buscador --}}
    <div class="mb-4 mt-4">
        <input type="text"
               wire:model.live="buscar"
               placeholder="Buscar material o código..."
               class="w-full md:w-1/3 border rounded px-3 py-2">
    </div>
   


    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="text-left p-3">Material</th>
                    <th class="text-left p-3">Tipo Material</th>
                    <th class="text-left p-3">Código</th>
                    <th class="text-left p-3">GCI Código</th>
                    <th class="text-center p-3">Stock</th>
                    <th class="text-center p-3">Stock mínimo</th>
                    <th class="text-center p-3">Esencial</th>
                    <th class="text-center p-3">Estado</th>
                    <th class="text-center p-3">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($materiales as $material)
                    <tr wire:key="material-{{ $material->id }}" class="border-b">
                        <td class="p-3 font-semibold">{{ $material->nombre }}</td>
                        <td class="p-3">
                            <select 
                                wire:change="cambiarTipoMaterial({{ $material->id }}, $event.target.value)"
                                class="border rounded px-2 py-1 text-sm w-full"
                            >
                                @foreach($tiposMateriales as $tipo)
                                    <option value="{{ $tipo }}" {{ optional($material->tipo)->nombre === $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-3">{{ $material->codigo_referencia }}</td>
                        <td class="p-3">{{ $material->gci_codigo ?? '---' }}</td>
                        <td class="p-3 text-center">{{ $material->stock_actual }}</td>
                        <td class="p-3 text-center">{{ $material->stock_minimo }}</td>
                        <td class="p-3 text-center">
                            <button
                                wire:click="toggleEsencial({{ $material->id }})"
                                class="px-3 py-1 rounded text-xs font-bold
                                    {{ $material->material_esencial 
                                        ? 'bg-blue-600 text-white' 
                                        : 'bg-gray-300 text-gray-700' }}">
                                {{ $material->material_esencial ? 'Esencial' : 'Normal' }}
                            </button>
                        </td>
                        <td class="p-3 text-center">
                            @if($material->stock_actual <= $material->stock_minimo)
                                <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">Stock bajo</span>
                            @else
                                <span class="bg-green-600 text-white px-2 py-1 rounded text-xs">OK</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            {{-- Botones --}}
                            <button wire:click="abrirModalIngreso({{ $material->id }})"
                                    class="bg-green-600 text-white px-3 py-1 rounded">
                                Ingreso
                            </button>
                            <button wire:click="abrirModalRetiro({{ $material->id }})"
                                    class="bg-orange-600 text-white px-3 py-1 rounded disabled:opacity-40"
                                    {{ $material->stock_actual <= 0 ? 'disabled' : '' }}>
                                Egreso
                            </button>
                            @if($material->stock_actual <= $material->stock_minimo)

                                @php
                                    $tienePedidoPendiente = \App\Models\Pedido::where('materiales_id', $material->id)
                                        ->where('estado', 'pendiente')
                                        ->exists();
                                @endphp

                                <button
                                    wire:click="abrirModalPedido({{ $material->id }})"
                                    @disabled($tienePedidoPendiente)
                                    class="px-3 py-1 rounded text-white
                                        {{ $tienePedidoPendiente ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600' }}">
                                    
                                    {{ $tienePedidoPendiente ? 'Pedido enviado' : 'Realizar pedido' }}

                                </button>

                            @endif

                            <button wire:click="editarMaterial({{ $material->id }})"
                                class="bg-blue-500 text-white px-3 py-1 rounded">
                                Editar
                            </button>

                            <button wire:click="abrirModalEliminarMaterial({{ $material->id }})"
                                    class="bg-red-600 text-white px-3 py-1 rounded">
                                Eliminar
                            </button>
                            </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center p-6 text-gray-500">
                            No hay materiales cargados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Retiro --}}
        @if($mostrarModalRetiro)

<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">

        <h2 class="text-lg font-bold mb-4">
            Retirar material: {{ $materialSeleccionado->nombre }}
        </h2>

        {{-- Cantidad --}}
        <div class="mb-3">
            <label class="text-sm font-semibold">Cantidad</label>
            <input type="number" wire:model="cantidadRetiro"
                class="w-full border rounded p-2">
        </div>

        {{-- Destino --}}
        <div class="mb-3">
            <label class="text-sm font-semibold">Destino</label>
            <input type="text" wire:model="destinoRetiro"
                class="w-full border rounded p-2">
        </div>

        {{-- Ticket --}}
        <div class="mb-3">
            <label class="text-sm font-semibold">N° Ticket</label>
            <input type="text" wire:model="ticketRetiro"
                class="w-full border rounded p-2">
        </div>

        {{-- Funcionario --}}
        <div class="mb-4">
            <label class="text-sm font-semibold">Funcionario</label>
            <select wire:model="funcionario_id" class="w-full border rounded p-2">
                <option value="">Seleccionar</option>
                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}">
                        {{ $funcionario->nombre }}
                        {{ $funcionario->apellido }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-2">

            <button
                wire:click="$set('mostrarModalRetiro', false)"
                class="bg-gray-400 text-white px-4 py-2 rounded">
                Cancelar
            </button>

            <button
                wire:click="guardarRetiro"
                class="bg-green-600 text-white px-4 py-2 rounded">
                Confirmar retiro
            </button>

        </div>

    </div>
</div>

@endif

    {{-- Modal Ingreso --}}
    @if($mostrarModalIngreso)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-lg font-bold mb-4">
                    Ingreso de stock: {{ $materialSeleccionado->nombre }}
                </h2>

                {{-- Stock --}}
                <div class="mb-3">
                    <label class="text-sm font-semibold">Stock</label>
                    <input type="number" wire:model="stockIngreso"
                        class="w-full border rounded p-2">
                </div>

                {{-- Stock mínimo --}}
                <div class="mb-3">
                    <label class="text-sm font-semibold">Stock mínimo</label>
                    <input type="number" wire:model="stockMinimoIngreso"
                        class="w-full border rounded p-2">
                </div>

                <div class="flex justify-end gap-2">
                    <button wire:click="$set('mostrarModalIngreso', false)"
                        class="bg-gray-400 text-white px-4 py-2 rounded">
                        Cancelar
                    </button>
                    <button wire:click="guardarIngreso"
                        class="bg-green-600 text-white px-4 py-2 rounded">
                        Confirmar ingreso
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($mostrarModalEditar)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

        <h2 class="text-lg font-bold mb-4">
            Editar material
        </h2>

        {{-- Nombre --}}
        <div class="mb-3">
            <label class="text-sm font-semibold">Nombre</label>
            <input type="text" wire:model="nombreEditar"
                class="w-full border rounded p-2">
        </div>

        {{-- Código --}}
        <div class="mb-4">
            <label class="text-sm font-semibold">Código</label>
            <input type="text" wire:model="codigoEditar"
                class="w-full border rounded p-2">
        </div>

        <div class="flex justify-end gap-2">

            <button
                wire:click="$set('mostrarModalEditar', false)"
                class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancelar
            </button>

            <button
                wire:click="guardarEdicionMaterial"
                class="bg-blue-600 text-white px-4 py-2 rounded">
                Guardar cambios
            </button>

        </div>

    </div>
</div>
@endif

@if($mostrarModalEliminarMaterial)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Eliminar material: {{ $materialSeleccionado->nombre }}</h2>

        <p class="mb-4">¿Estás seguro de que deseas eliminar este material? Esta acción no se puede deshacer.</p>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalEliminarMaterial', false)" 
                    class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancelar
            </button>
            <button wire:click="confirmarEliminarMaterial" 
                    class="bg-red-600 text-white px-4 py-2 rounded">
                Eliminar
            </button>
        </div>
    </div>
</div>
@endif

<div class="p-6 mt-6">

    {{-- Botón abrir/cerrar estadísticas --}}
    <button wire:click="$toggle('mostrarEstadisticas')"
        class="flex items-center bg-blue-600 text-white px-4 py-2 rounded mb-2">
        <span class="mr-2 font-semibold text-lg">Estadísticas de Materiales</span>
        <svg class="w-5 h-5 transition-transform duration-300 {{ $mostrarEstadisticas ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Contenedor estadístico --}}
    @if($mostrarEstadisticas)
        <div class="bg-white dark:bg-gray-800 shadow rounded p-6 mt-2">

            {{-- Filtros estadísticos --}}
            <div class="flex flex-wrap gap-4 mb-4 items-end">

                <div>
                    <label class="text-sm font-semibold">Desde:</label>
                    <input type="date" wire:model.live="filtroEstDesde" class="border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-semibold">Hasta:</label>
                    <input type="date" wire:model.live="filtroEstHasta" class="border rounded p-2">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold">Material:</label>
                    <input type="text" wire:model.live="filtroEstBusqueda" placeholder="Buscar material o código" class="border rounded p-2 w-full">
                </div>

            </div>
            <div class="flex gap-2 mb-3">
    <button wire:click="exportarEstadisticasCsv"
        class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
        Exportar Estadísticas a CSV
    </button>

    <button wire:click="exportarEstadisticasPdf"
        class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
        Exportar Estadísticas a PDF
    </button>
</div>


            {{-- Tabla de consumo --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="text-left p-3">Material</th>
                            <th class="text-left p-3">Código</th>
                            <th class="text-center p-3">Total Consumido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->estadisticasMasConsumidos as $item)
                            <tr class="border-b">
                                <td class="p-3 font-semibold">
                                    {{ $item->material->nombre ?? '---' }}
                                </td>

                                <td class="p-3">
                                    {{ $item->material->codigo_referencia ?? '---' }}
                                </td>

                                <td class="p-3 text-center font-bold">
                                    {{ $item->total_consumido }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center p-6 text-gray-500">
                                    No hay consumo registrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @endif

</div>


<div class="p-6 mt-10">

    {{-- Botón para abrir/cerrar historial --}}
    <button wire:click="toggleHistorial"
        class="flex items-center bg-blue-600 text-white px-4 py-2 rounded mb-2">
        <span class="mr-2 font-semibold text-lg">Historial de movimientos</span>
        <svg class="w-5 h-5 transition-transform duration-300 {{ $historialAbierto ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Contenedor del historial --}}
    @if($historialAbierto)
        <div class="bg-white dark:bg-gray-800 shadow rounded p-6 mt-2">

            {{-- Filtros --}}
            <div class="flex flex-wrap gap-4 mb-4 items-end">
                <div>
                    <label class="text-sm font-semibold">Desde:</label>
                    <input type="date" wire:model.live="filtroDesde" class="border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-semibold">Hasta:</label>
                    <input type="date" wire:model.live="filtroHasta" class="border rounded p-2">
                </div>

                <div>
                    <label class="text-sm font-semibold">Tipo:</label>
                    <select wire:model.live="filtroTipo" class="border rounded p-2">
                        <option value="">Todos</option>
                        <option value="creacion">Creación</option>
                        <option value="entrada">Ingreso</option>
                        <option value="salida">Egreso</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold">Material:</label>
                    <input type="text" wire:model.live="filtroBusqueda" placeholder="Buscar material o código" class="border rounded p-2 w-full">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold">Destino:</label>
                    <input type="text" wire:model.live="filtroDestino" placeholder="Buscar por destino" class="border rounded p-2 w-full">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold">N° Ticket:</label>
                    <input type="text" wire:model.live="filtroTicket" placeholder="Buscar por ticket" class="border rounded p-2 w-full">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-semibold">Funcionario:</label>
                    <input type="text" wire:model.live="filtroFuncionario" placeholder="Buscar por funcionario" class="border rounded p-2 w-full">
                </div>
            </div>
            <div class="flex gap-2 mb-3">
    <button wire:click="exportarHistorialCsv"
        class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
        Exportar Historial a CSV
    </button>

<a href="{{ route('historial.exportar') }}" class="btn btn-danger bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
    Exportar Materiales a PDF
</a>
</div>

            {{-- Tabla de movimientos --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="text-left p-3">Fecha</th>
                            <th class="text-left p-3">Material</th>
                            <th class="text-left p-3">Tipo</th>
                            <th class="text-center p-3">Cantidad</th>
                            <th class="text-left p-3">Notas / Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $mov)
                            <tr class="border-b">
                                <td class="p-3">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">{{ $mov->material->nombre ?? '---' }} ({{ $mov->material->codigo_referencia ?? '---' }})</td>
                                <td class="p-3 capitalize">{{ $mov->tipo }}</td>
                                <td class="p-3 text-center">{{ $mov->cantidad }}</td>
                                <td class="p-3">
                                    Motivo: {{ $mov->motivo ?? '---' }}<br>
                                    Destino: {{ $mov->destino ?? '---' }}<br>
                                    N° Ticket: {{ $mov->ticket ?? '---' }}<br>
                                    Funcionario: {{ $mov->funcionario?->nombre ?? '' }} {{ $mov->funcionario?->apellido ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-6 text-gray-500">
                                    No hay movimientos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @endif
</div>
@if($mostrarModalPedido)
<div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white rounded shadow-lg p-6 w-full max-w-md">

        <h2 class="text-lg font-bold mb-4">
            Realizar pedido de material
        </h2>

        {{-- Cantidad --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold mb-1">
                Cantidad a pedir
            </label>
            <input type="number"
                   wire:model="cantidadPedido"
                   min="1"
                   class="border rounded p-2 w-full">
        </div>

        {{-- SKU --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold mb-1">
                SKU (opcional)
            </label>
            <input type="text"
                   wire:model="skuPedido"
                   class="border rounded p-2 w-full">
        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-2">

            <button
                wire:click="$set('mostrarModalPedido', false)"
                class="px-3 py-2 bg-gray-400 text-white rounded">
                Cancelar
            </button>

            <button
                wire:click="guardarPedidoMaterial"
                class="px-3 py-2 bg-blue-600 text-white rounded">
                Enviar pedido
            </button>

        </div>

    </div>
</div>
@endif
<script>
    window.addEventListener('reload-page', event => {
        // Espera 5 segundos (5000 ms) antes de recargar
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
</script>

</div>
