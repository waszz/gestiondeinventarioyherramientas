<div  class="p-6">

    <h2 class="text-2xl font-bold mb-6">Gestión de Herramientas</h2>



    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('panol.herramientas.crear') }}"
     class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
    + Añadir Nueva Herramienta
  </a>
  <a href="#" 
   wire:click="abrirModalPedido"
   class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
   + Realizar Pedido
</a>

<button wire:click="exportarHerramientasCsv"
    class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
   Exportar Herramientas a CSV
</button>
<button wire:click="exportarHerramientasPdf"
    class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
   Exportar Herramientas a PDF
</button>
<div class="mb-4 flex flex-wrap gap-4 items-center">
    <input type="text" wire:model.live="buscar" placeholder="Buscar por nombre o código"
           class="border rounded px-3 py-2 w-full md:w-1/5">

    <select wire:model.live="filtroEstado" class="border rounded px-3 py-2 w-full md:w-1/5">
        <option value="">Todos los estados</option>
        <option value="disponible">Disponibles</option>
        <option value="prestamo">En préstamo</option>
        <option value="fuera_servicio">Fuera de servicio</option>
    </select>

   @php
    $stockActual = \App\Models\Bateria::first()?->stock_total ?? 0;

    $colorBadge = $stockActual < 5 
        ? 'bg-red-100 text-red-700'
        : ($stockActual < 10 
            ? 'bg-yellow-100 text-yellow-700'
            : 'bg-green-100 text-green-700');
@endphp

<div class="mb-6 p-5 bg-white shadow-sm rounded-xl border border-gray-200 flex flex-wrap items-center justify-between gap-6">

    {{-- Stock visual --}}
    <div class="flex items-center gap-4">

        <div>
            <div class="text-sm text-gray-500 font-semibold">
                Stock actual de baterías
            </div>

            <div class="mt-1">
                <span class="px-3 py-2 rounded-full text-lg font-bold {{ $colorBadge }}">
                    {{ $stockActual }}
                </span>
            </div>
        </div>

    </div>

    {{-- Editor --}}
    <div class="flex items-end gap-3">

        <div class="flex flex-col">
            <label class="text-sm font-semibold text-gray-600 mb-1">
                Modificar stock
            </label>
           <input 
                type="number"
                wire:model.defer="nuevoStockBaterias"
                step="1"
                class="w-32 px-3 py-2 text-sm border border-gray-300 rounded-lg 
                    focus:outline-none focus:ring-2 focus:ring-blue-500 
                    focus:border-blue-500 transition"
            >
        </div>

        <button 
            wire:click="guardarStockBaterias"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm 
                   px-4 py-2 rounded-lg shadow-sm transition">
            Guardar
        </button>

    </div>

</div>
    <!-- Botones de acciones múltiples -->
    <button wire:click="abrirModalPrestamoMultiple"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">
        Préstamo múltiple
    </button>

    <button wire:click="abrirModalDevolucionMultiple"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold">
        Devolución múltiple
    </button>
</div>


    <div class="bg-white dark:bg-gray-800 shadow rounded overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Código</th>
                    <th class="text-left p-3">GCI Código</th>
                    <th class="text-center p-3">Alimentación</th>
                    <th class="text-center p-3">Cantidad Total</th>
                    <th class="text-center p-3">Estado</th>
                    <th class="text-center p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($herramientas as $herramienta)
                    <tr class="border-b">
                        <td class="p-3">{{ $herramienta->nombre }}</td>
                        <td class="p-3">{{ $herramienta->codigo }}</td>
                        <td class="p-3">{{ $herramienta->gci_codigo }}</td> 
                        <td class="p-3 text-center">
    @if($herramienta->tipo_alimentacion === 'bateria')
        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm font-semibold">
            Batería
        </span>
    @elseif($herramienta->tipo_alimentacion === 'cable')
        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm font-semibold">
             Cable
        </span>
    @else
        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm">
            Manual
        </span>
    @endif
</td>
                        <td class="p-3 text-center">{{ $herramienta->cantidad }}</td>
             <td class="p-3">
    <div class="flex flex-col items-center">
        {{-- Disponible --}}
        <div class="text-green-600 font-semibold mb-1">
            Disponible: {{ $herramienta->cantidad_disponible }}
        </div>

{{-- En préstamo con toggle --}}

<div class="text-blue-600 font-semibold w-full mb-1">
    {{-- Toggle de herramientas --}}
    <button wire:click="togglePrestamos({{ $herramienta->id }})"
            class="flex justify-center items-center gap-1 w-full">
        En préstamo (herramientas): {{ $herramienta->cantidad_prestamo }}
        <span>
            @if(isset($mostrarPrestamos[$herramienta->id]))
                &#9650; {{-- flecha arriba --}}
            @else
                &#9660; {{-- flecha abajo --}}
            @endif
        </span>
    </button>

    @if(isset($mostrarPrestamos[$herramienta->id]))
        <ul class="mt-1 text-sm text-center text-gray-700 px-2">
            @php
                $prestamosActivos = $herramienta->prestamos->where('estado', 'prestada');
                $prestamosAgrupados = $prestamosActivos->groupBy('funcionario_id')->map(function($items) {
                    return [
                        'cantidad' => $items->sum('cantidad'),
                        'fecha' => $items->first()->created_at,
                        'funcionario' => $items->first()->funcionario,
                    ];
                });
            @endphp

            @forelse($prestamosAgrupados as $prestamo)
                <li>
                    {{ $prestamo['funcionario']->nombre }} {{ $prestamo['funcionario']->apellido }}
                    (desde: {{ $prestamo['fecha']->format('d/m/Y') }})
                    - Cantidad: {{ $prestamo['cantidad'] }}
                </li>
            @empty
                <li>Ningún préstamo activo</li>
            @endforelse
        </ul>
    @endif
</div>

<div class="text-green-600 font-semibold w-full mb-1">
    {{-- Toggle de baterías --}}
    <button wire:click="togglePrestamosBaterias({{ $herramienta->id }})"
            class="flex justify-center items-center gap-1 w-full">
        @php
    $bateriasActivas = $herramienta->prestamos->where('estado', 'prestada')->sum('cantidad_baterias');
@endphp
En préstamo (baterías): {{ $bateriasActivas }}
        <span>
            @if(isset($mostrarPrestamosBaterias[$herramienta->id]))
                &#9650; {{-- flecha arriba --}}
            @else
                &#9660; {{-- flecha abajo --}}
            @endif
        </span>
    </button>

    @if(isset($mostrarPrestamosBaterias[$herramienta->id]))
        <ul class="mt-1 text-sm text-center text-gray-700 px-2">
            @php
                $prestamosActivos = $herramienta->prestamos->where('estado', 'prestada')->where('cantidad_baterias', '>', 0);
                $prestamosAgrupados = $prestamosActivos->groupBy('funcionario_id')->map(function($items) {
                    return [
                        'cantidad_baterias' => $items->sum('cantidad_baterias'),
                        'fecha' => $items->first()->created_at,
                        'funcionario' => $items->first()->funcionario,
                    ];
                });
            @endphp

            @forelse($prestamosAgrupados as $prestamo)
                <li>
                    {{ $prestamo['funcionario']->nombre }} {{ $prestamo['funcionario']->apellido }}
                    (desde: {{ $prestamo['fecha']->format('d/m/Y') }})
                    - Baterías: {{ $prestamo['cantidad_baterias'] }}
                </li>
            @empty
                <li>Ningún préstamo de baterías</li>
            @endforelse
        </ul>
    @endif
</div>
        {{-- Fuera de servicio --}}
   <div class="text-red-600 font-semibold">
    <button wire:click="toggleFueraServicio({{ $herramienta->id }})" class="flex items-center gap-1 justify-center w-full">
        Fuera de servicio: {{ $herramienta->cantidad_fuera_servicio }}
        <span>
            @if(isset($mostrarFueraServicio[$herramienta->id]))
                &#9650; {{-- flecha arriba --}}
            @else
                &#9660; {{-- flecha abajo --}}
            @endif
        </span>
    </button>

    @if(isset($mostrarFueraServicio[$herramienta->id]))
        <ul class="mt-2 space-y-1 text-sm text-gray-700 text-center">
            @foreach($herramienta->fueraServicios as $fuera)
                <li class="flex justify-center items-center gap-2">
                    <span>
                        {{ $fuera->cantidad }} - {{ $fuera->motivo }} 
                        (desde: {{ $fuera->created_at->format('d/m/Y') }})
                    </span>
                    <button wire:click="restaurarFueraServicio({{ $fuera->id }})"
                            class="bg-green-500 text-white px-2 py-0.5 rounded text-xs">
                        Restaurar
                    </button>
                </li>
            @endforeach
            @if($herramienta->fueraServicios->isEmpty())
                <li>Ningún registro</li>
            @endif
        </ul>
    @endif
</div>
    </div>
</td>

                        <td class="p-3 text-center flex flex-wrap gap-2 justify-center">
                            <button wire:click="abrirModalPrestamo({{ $herramienta->id }})"
                                    class="bg-green-600 text-white px-3 py-1 rounded"
                                    {{ $herramienta->estado != 'disponible' ? 'disabled' : '' }}>
                                Prestar
                            </button>
   @php
    // Herramientas pendientes
    $herramientasPendientes = $herramienta->cantidad_prestamo > 0;

    // Baterías prestadas activas
    $prestamosActivos = $herramienta->prestamos->where('estado', 'prestada');
    $bateriasPendientes = $prestamosActivos->sum('cantidad_baterias');

    // Activar botón si hay herramientas o baterías pendientes
    $pendienteDevolver = $herramientasPendientes || $bateriasPendientes > 0;
@endphp

<button wire:click="abrirModalDevolver({{ $herramienta->id }})"
        class="bg-blue-600 text-white px-3 py-1 rounded disabled:opacity-50"
        {{ $pendienteDevolver ? '' : 'disabled' }}>
    Devolver
</button>
                            <button wire:click="abrirModalFueraServicio({{ $herramienta->id }})"
                                    class="bg-red-600 text-white px-3 py-1 rounded"
                                    {{ $herramienta->estado == 'fuera_servicio' ? 'disabled' : '' }}>
                                Fuera de servicio
                            </button>
                            
                            <button wire:click="abrirModalEditar({{ $herramienta->id }})"
        class="bg-blue-500 text-white px-3 py-1 rounded">
    Editar
</button>
                            <button wire:click="abrirModalEliminar({{ $herramienta->id }})"
        class="bg-red-500 text-white px-3 py-1 rounded">
    Eliminar
</button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-6 text-gray-500">
                            No hay herramientas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <button wire:click="toggleEstadisticas"
       class="flex items-center bg-blue-600 text-white px-4 py-2 rounded mb-2 mt-2">
    
     <span class="mr-2 font-semibold text-lg">Estadísticas de Herramientas</span>

    <svg class="w-5 h-5 transition-transform duration-300 {{ $estadisticasAbiertas ? 'rotate-180' : '' }}"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7" />
    </svg>

</button>

@if($estadisticasAbiertas)

<div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-4">

    <h2 class="text-lg font-bold mb-4">Estadísticas de uso de herramientas</h2>
    <button wire:click="exportarEstadisticasHerramientasCsv"
    class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 mb-4 rounded-lg">
   Exportar Estadísticas de  Herramientas a CSV
</button>

  <button wire:click="exportarEstadisticasHerramientasPdf"
    class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 mb-4 rounded-lg">
   Exportar Estadísticas de  Herramientas a PDF
</button>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="text-left p-3">Herramientas más utilizadas</th>
                    <th class="text-left p-3">Herramientas menos utilizadas</th>
                    <th class="text-left p-3">Funcionarios que más usan</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $max = max(
                        $herramientasMasUsadas->count(),
                        $herramientasMenosUsadas->count(),
                        $funcionariosUso->count()
                    );
                @endphp

                @for($i = 0; $i < $max; $i++)
                    <tr class="border-b">

                        {{-- MÁS USADAS --}}
                        <td class="p-3">
                            @if(isset($herramientasMasUsadas[$i]))
                                <div class="font-semibold">
                                    {{ $herramientasMasUsadas[$i]->nombre }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $herramientasMasUsadas[$i]->total_usos }} usos
                                </div>
                            @endif
                        </td>

                        {{-- MENOS USADAS --}}
                        <td class="p-3">
                            @if(isset($herramientasMenosUsadas[$i]))
                                <div class="font-semibold">
                                    {{ $herramientasMenosUsadas[$i]->nombre }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $herramientasMenosUsadas[$i]->total_usos }} usos
                                </div>
                            @endif
                        </td>

                        {{-- FUNCIONARIOS --}}
                        <td class="p-3">
                            @if(isset($funcionariosUso[$i]))
                                <div class="font-semibold">
                                    {{ $funcionariosUso[$i]->funcionario }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $funcionariosUso[$i]->total }} usos
                                </div>
                            @endif
                        </td>

                    </tr>
                @endfor

                @if($max === 0)
                    <tr>
                        <td colspan="3" class="text-center p-6 text-gray-500">
                            No hay estadísticas registradas todavía
                        </td>
                    </tr>
                @endif

            </tbody>

        </table>
    </div>

</div>

@endif




   <button wire:click="toggleHistorial"
    class="flex items-center bg-blue-600 text-white px-4 py-2 rounded mb-2 mt-2">
    
    <span class="mr-2 font-semibold text-lg">Historial de Herammientas</span>

    <svg class="w-5 h-5 transition-transform duration-300 {{ $historialAbierto ? 'rotate-180' : '' }}"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 9l-7 7-7-7" />
    </svg>
</button>

@if($historialAbierto)

<div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-4">

    {{-- FILTROS --}}
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
                <option value="prestamo">Préstamo</option>
                <option value="devolucion">Devolución</option>
                <option value="prestamo_multiple">Préstamo múltiple</option>
                <option value="devolucion_multiple">Devolución múltiple</option>
                <option value="fuera_servicio">Fuera de servicio</option>
                <option value="restaurado">Restaurado</option>
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="text-sm font-semibold">Herramienta:</label>
            <input type="text" wire:model.live="filtroBusqueda"
                   placeholder="Buscar nombre o código"
                   class="border rounded p-2 w-full">
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="text-sm font-semibold">Funcionario:</label>
            <input type="text" wire:model.live="filtroFuncionario"
                   placeholder="Buscar funcionario"
                   class="border rounded p-2 w-full">
        </div>

    </div>

    <button wire:click="exportarHistorialHerramientasCsv"
    class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 mb-4 rounded-lg">
   Exportar Historial de Herramientas a CSV
</button>

  <button wire:click="exportarHistorialHerramientasPdf"
    class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 mb-4 rounded-lg">
   Exportar Historial de Herramientas a PDF
</button>
    {{-- TABLA --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-left p-3">Herramienta</th>
                    <th class="text-left p-3">Tipo</th>
                    <th class="px-3 py-2">Baterías</th>
                    <th class="text-left p-3">Funcionario</th>
                    <th class="text-left p-3">Detalle</th>
                </tr>
            </thead>

            <tbody>
                @forelse($historial as $item)
                    <tr class="border-b">

                        <td class="p-3">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="p-3">
                            {{ $item->nombre }}
                            <br>
                            <span class="text-xs text-gray-500">
                                {{ $item->codigo }}
                            </span>
                        </td>

                        <td class="p-3 capitalize">
                            {{ str_replace('_',' ', $item->tipo) }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($item->cantidad_baterias > 0)
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 
                                            px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ $item->cantidad_baterias }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="p-3">
                            {{ $item->funcionario ?? 'N/A' }}
                        </td>

                        <td class="p-3 text-gray-600">
    <div>{{ $item->detalle }}</div>

    @if($item->observacion)
        <div class="text-xs text-gray-600 mt-1">
            Obs: {{ $item->observacion }}
        </div>
    @endif
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-6 text-gray-500">
                            No hay movimientos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

@endif



    @if($mostrarModalEditar)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Editar herramienta: {{ $herramientaSeleccionada->nombre }}</h2>

        <div class="mb-3">
            <label class="text-sm font-semibold">Nombre</label>
            <input type="text" wire:model="nombreHerramienta" class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label class="text-sm font-semibold">Código</label>
            <input type="text" wire:model="codigoHerramienta" class="w-full border rounded p-2">
        </div>

        <div class="mb-3">
            <label class="text-sm font-semibold">Cantidad Total</label>
            <input type="number" wire:model="cantidadHerramienta" class="w-full border rounded p-2" min="0">
        </div>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalEditar', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            <button wire:click="actualizarHerramienta" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar Cambios</button>
        </div>
    </div>
</div>
@endif

    @if($mostrarModalPrestamo)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Prestar herramienta: {{ $herramientaSeleccionada->nombre }}</h2>

        <div class="mb-3">
            <label class="text-sm font-semibold">Cantidad</label>
            <input type="number" wire:model="cantidadPrestamo" class="w-full border rounded p-2" min="1" max="{{ $herramientaSeleccionada->cantidad }}">
        </div>
   @if($herramientaSeleccionada->tipo_alimentacion === 'bateria')
    <div class="mb-3">
        <label class="text-sm font-semibold">
            Cantidad de baterías (Disponibles: {{ $stockBaterias }})
        </label>
        <input type="number"
               wire:model="cantidadBateriasPrestamo"
               class="w-full border rounded p-2"
               min="0"
               max="{{ $stockBaterias }}">
    </div>
@endif

        <div class="mb-4">
            <label class="text-sm font-semibold">Funcionario</label>
            <select wire:model="funcionario_id" class="w-full border rounded p-2">
                <option value="">Seleccionar</option>
                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}">{{ $funcionario->nombre }} {{ $funcionario->apellido }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalPrestamo', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            <button wire:click="prestarHerramienta" class="bg-blue-600 text-white px-4 py-2 rounded">Prestar</button>
        </div>
    </div>
</div>
@endif

@if($mostrarModalDevolver)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Devolver herramienta: {{ $herramientaSeleccionada->nombre }}</h2>

        <div class="mb-3">
            <label class="text-sm font-semibold">Cantidad a devolver</label>
            <input type="number" wire:model="cantidadPrestamo" class="w-full border rounded p-2" min="1" max="{{ $herramientaSeleccionada->cantidad_prestamo }}">
        </div>

        {{--SOLO SI ES A BATERÍA --}}
@if($herramientaSeleccionada->tipo_alimentacion === 'bateria')

    @php
        $prestamosActivos = $herramientaSeleccionada->prestamos
            ->where('estado', 'prestada');

        $totalBateriasPrestadas = $prestamosActivos->sum('cantidad_baterias');
    @endphp

    <div class="mb-3">
        <label class="text-sm font-semibold">
            Cantidad de baterías a devolver
            <span class="text-xs text-gray-500">
                (Prestadas: {{ $totalBateriasPrestadas }})
            </span>
        </label>

        <input type="number"
               wire:model="cantidadBateriasDevolucion"
               min="0"
               max="{{ $totalBateriasPrestadas }}"
               class="w-full border rounded p-2">
    </div>

@endif

    <div class="mb-3">
    <label class="text-sm font-semibold">¿Quién devuelve la herramienta?</label>
    <select wire:model="funcionarioPrestamoId" class="w-full border rounded p-2">
        <option value="">Seleccionar</option>

        @php
            // Filtrar solo préstamos activos
            $prestamosActivos = $herramientaSeleccionada->prestamos->where('estado', 'prestada');

            // Agrupar por funcionario y sumar cantidades
            $prestamosAgrupados = $prestamosActivos->groupBy('funcionario_id')->map(function($items) {
                return [
                    'cantidad' => $items->sum('cantidad'),
                    'funcionario' => $items->first()->funcionario,
                    'fecha' => $items->first()->created_at, // fecha del primer préstamo
                    'prestamo_ids' => $items->pluck('id')->toArray(), // opcional si necesitas IDs
                ];
            });
        @endphp

        @foreach($prestamosAgrupados as $id => $prestamo)
            <option value="{{ implode(',', $prestamo['prestamo_ids']) }}">
    {{ $prestamo['funcionario']->nombre }} {{ $prestamo['funcionario']->apellido }}
    (Prestado: {{ $prestamo['cantidad'] }} desde {{ $prestamo['fecha']->format('d/m/Y') }})
</option>
        @endforeach

        @if($prestamosAgrupados->isEmpty())
            <option value="">No hay préstamos activos</option>
        @endif
    </select>
</div>
        <div class="mb-3">
            <label class="text-sm font-semibold">Observaciones</label>
            <textarea wire:model="observacionesDevolucion" class="w-full border rounded p-2" rows="2" placeholder="Opcional"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalDevolver', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            <button wire:click="devolverHerramienta" class="bg-blue-600 text-white px-4 py-2 rounded">Confirmar Devolución</button>
        </div>
    </div>
</div>
@endif

@if($mostrarModalFueraServicio)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Fuera de servicio: {{ $herramientaSeleccionada->nombre }}</h2>

        <div class="mb-3">
            <label class="text-sm font-semibold">Cantidad</label>
            <input type="number" wire:model="cantidadPrestamo"
                   class="w-full border rounded p-2"
                   min="1" max="{{ $herramientaSeleccionada->cantidad_disponible }}">
        </div>

        <div class="mb-3">
            <label class="text-sm font-semibold">Motivo</label>
            <input type="text" wire:model="motivoFueraServicio"
                   class="w-full border rounded p-2" placeholder="Ej: Dañada">
        </div>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalFueraServicio', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            <button wire:click="marcarFueraServicio" class="bg-blue-600 text-white px-4 py-2 rounded">Confirmar</button>
        </div>
    </div>
</div>
@endif

@if($mostrarModalEliminar)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Eliminar herramienta: {{ $herramientaSeleccionada->nombre }}</h2>

        <p class="mb-4">¿Estás seguro de que deseas eliminar esta herramienta? Esta acción no se puede deshacer.</p>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalEliminar', false)" 
                    class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancelar
            </button>
            <button wire:click="confirmarEliminarHerramienta" 
                    class="bg-red-600 text-white px-4 py-2 rounded">
                Eliminar
            </button>
        </div>
    </div>
</div>
@endif

@if($mostrarModalPrestamoMultiple)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
        <h2 class="text-lg font-bold mb-4">Préstamo múltiple de herramientas</h2>

        <div class="mb-3">
            <label>Funcionario</label>
            <select wire:model="funcionario_id" class="w-full border rounded p-2">
                <option value="">Seleccionar</option>
                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}">{{ $funcionario->nombre }} {{ $funcionario->apellido }}</option>
                @endforeach
            </select>
        </div>

       <div class="mb-4 p-3 bg-gray-50 rounded-lg border text-sm">
    <span class="font-semibold">Stock actual de baterías:</span>
    <span class="ml-2 font-bold">{{ $stockBaterias }}</span>
</div>

<div class="space-y-3 max-h-80 overflow-y-auto pr-2">

@foreach($herramientas as $herramienta)

    <div class="border rounded-lg p-3">

        <div class="flex justify-between items-center mb-2">
            <span class="font-semibold">
                {{ $herramienta->nombre }}
                <span class="text-sm text-gray-500">
                    (Disponible: {{ $herramienta->cantidad_disponible }})
                </span>
            </span>
        </div>

        <div class="flex gap-6 items-end">

            {{-- Cantidad herramienta --}}
            <div>
                <label class="text-xs text-gray-500">Cantidad herramienta</label>
                <input type="number"
                       min="0"
                       max="{{ $herramienta->cantidad_disponible }}"
                       wire:model="prestamosMultiple.{{ $herramienta->id }}"
                       class="w-20 border rounded px-2 py-1">
            </div>

            {{-- SOLO SI ES A BATERÍA --}}
            @if($herramienta->tipo_alimentacion === 'bateria')
                <div>
                    <label class="text-xs text-gray-500">
                        Baterías
                    </label>
                    <input type="number"
                           min="0"
                           max="{{ $stockBaterias }}"
                           wire:model="bateriasMultiple.{{ $herramienta->id }}"
                           class="w-24 border rounded px-2 py-1">
                </div>
            @endif

        </div>

    </div>

@endforeach

</div>

        <div class="flex justify-end gap-2 mt-4">
            <button wire:click="$set('mostrarModalPrestamoMultiple', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            <button wire:click="prestarHerramientasMultiple" class="bg-blue-600 text-white px-4 py-2 rounded">Prestar</button>
        </div>
    </div>
</div>
@endif

@if($mostrarModalDevolucionMultiple)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">

        <h2 class="text-lg font-bold mb-4">
            Devolución múltiple de herramientas
        </h2>

        {{-- FUNCIONARIO --}}
        <div class="mb-3">
            <label class="block mb-1 text-sm font-medium">
                Funcionario
            </label>

            <select wire:model.live="funcionarioMultipleId"
                    class="w-full border rounded p-2">
                <option value="">Seleccionar</option>

                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}">
                        {{ $funcionario->nombre }} {{ $funcionario->apellido }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- OBSERVACIONES --}}
        <div class="mb-3">
            <label class="block mb-1 text-sm font-medium">
                Observaciones
            </label>

            <textarea wire:model="observacionesMultiple"
                      class="w-full border rounded p-2"
                      rows="2">
            </textarea>
        </div>

        <div class="space-y-3">

            {{-- SI NO HAY FUNCIONARIO --}}
            @if(!$funcionarioMultipleId)

                <p class="text-gray-500 text-sm">
                    Selecciona un funcionario para ver sus herramientas prestadas.
                </p>

            @else

                {{-- SI NO TIENE PRÉSTAMOS --}}
                @if($prestamosFuncionarioSeleccionado->isEmpty())

                    <p class="text-gray-500 text-sm">
                        Este funcionario no tiene herramientas prestadas.
                    </p>

                @else

                    {{-- LISTADO --}}
                    @foreach($prestamosFuncionarioSeleccionado as $prestamo)

                        <div wire:key="prestamo-{{ $prestamo->id }}"
                             class="border rounded p-3 space-y-2">

                            {{-- INFO HERRAMIENTA --}}
                            <div>
                                <span class="font-semibold">
                                    {{ $prestamo->herramienta->nombre }}
                                </span>

                                <div class="text-sm text-gray-500">
                                    Herramientas prestadas:
                                    {{ $prestamo->cantidad }}

                                    @if(strtolower($prestamo->herramienta->tipo_alimentacion) == 'bateria')
                                        | Baterías prestadas:
                                        {{ $prestamo->cantidad_baterias }}
                                    @endif
                                </div>
                            </div>

                            {{-- CANTIDAD A DEVOLVER --}}
                            <div class="flex items-center justify-between">
                                <label class="text-sm">
                                    Cantidad a devolver
                                </label>

                                <input type="number"
                                       min="0"
                                       max="{{ $prestamo->cantidad }}"
                                       wire:model="devolucionesMultiple.{{ $prestamo->id }}"
                                       class="w-20 border rounded px-2 py-1">
                            </div>

                            {{-- BATERÍAS --}}
                            @if(strtolower($prestamo->herramienta->tipo_alimentacion) == 'bateria')

                                <div class="flex items-center justify-between">
                                    <label class="text-sm">
                                        Baterías a devolver
                                        <span class="text-xs text-gray-500">
                                            (Prestadas: {{ $prestamo->cantidad_baterias }})
                                        </span>
                                    </label>

                                    <input type="number"
                                           min="0"
                                           max="{{ $prestamo->cantidad_baterias }}"
                                           wire:model="bateriasDevolucionesMultiple.{{ $prestamo->id }}"
                                           class="w-20 border rounded px-2 py-1">
                                </div>

                            @endif

                        </div>

                    @endforeach

                @endif

            @endif

        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-2 mt-4">

            <button
                wire:click="$set('mostrarModalDevolucionMultiple', false)"
                class="bg-gray-500 text-white px-4 py-2 rounded">
                Cancelar
            </button>

            @if($funcionarioMultipleId)
                <button
                    wire:click="devolverHerramientasMultiple"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                    Devolver
                </button>
            @endif

        </div>

    </div>
</div>
@endif

@if($mostrarModalPedido)
<div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-lg font-bold mb-4">Realizar pedido de herramienta</h2>

        <div class="mb-3">
            <label class="text-sm font-semibold">Herramienta</label>
            <select wire:model.live="herramientaPedidoId" class="w-full border rounded p-2">
                <option value="">Seleccionar</option>
                @foreach($herramientas as $herramienta)
                    <option value="{{ $herramienta->id }}">{{ $herramienta->nombre }} (Disponible: {{ $herramienta->cantidad_disponible }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="text-sm font-semibold">Cantidad</label>
            <input type="number" wire:model="cantidadPedido" class="w-full border rounded p-2" min="1">
        </div>
          
        <div class="mb-3">
            <label class="text-sm font-semibold">Observación</label>
            <textarea wire:model="observacionPedido" class="w-full border rounded p-2" rows="2" placeholder="Opcional"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button wire:click="$set('mostrarModalPedido', false)" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            <button wire:click="realizarPedido" class="bg-blue-600 text-white px-4 py-2 rounded">Realizar Pedido</button>
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
