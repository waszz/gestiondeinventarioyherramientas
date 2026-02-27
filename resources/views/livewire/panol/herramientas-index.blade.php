<div class="p-6 space-y-6">

    <h2 class="text-2xl font-bold mb-6">Gestión de Herramientas</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-4 mb-6">
        <a href="{{ route('panol.herramientas.crear') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-2xl font-semibold">
           + Añadir Nueva Herramienta
        </a>
        <a href="#" wire:click="abrirModalPedido"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-2xl font-semibold">
           + Realizar Pedido
        </a>
        <button wire:click="exportarHerramientasCsv"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-2xl font-semibold">
            Exportar Herramientas a CSV
        </button>
        <button wire:click="exportarHerramientasPdf"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-2xl font-semibold">
            Exportar Herramientas a PDF
        </button>
    </div>

 {{-- Importación --}}
<div class="flex bg-white p-2 rounded-2xl shadow-sm border border-gray-100">

    <input type="file"
           wire:model="archivoImportacion"
           class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-full">

    <button wire:click="importarHerramientas"
            class="bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-black transition-colors">

        <svg class="w-5 h-5"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>

    </button>

</div>

@if($columnasDetectadas)

<div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 space-y-4">

    <h3 class="text-lg font-semibold mb-2">
        Configurar Columnas
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        @foreach([
            'Nombre'          => 'columnaNombre',
            'Código'          => 'columnaCodigo',
            'GCI Código'      => 'columnaGci',
            'Alimentación'    => 'columnaAlimentacion',
            'Cantidad Total'  => 'columnaCantidad',
        ] as $label => $model)

        <div>
            <label class="block text-sm font-medium mb-1">
                {{ $label }}

                {{-- Obligatorios --}}
                @if(in_array($model, ['columnaNombre','columnaCodigo','columnaCantidad']))
                    <span class="text-red-500">*</span>
                @endif
            </label>

            <select wire:model="{{ $model }}"
                class="w-full border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

                <option value="">Seleccionar columna</option>

                @foreach($columnasDetectadas as $col)
                    <option value="{{ $col }}">
                        {{ $col }}
                    </option>
                @endforeach

            </select>
        </div>

        @endforeach

    </div>

    {{-- Error de duplicados --}}
    @error('duplicado')
        <span class="text-red-500 text-sm block">
            {{ $message }}
        </span>
    @enderror

    <div class="flex justify-end mt-4">
        <button wire:click="importarHerramientas"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow font-semibold transition">

            Importar Herramientas

        </button>
    </div>

</div>

@endif

    <div class="flex flex-wrap gap-4 items-center mb-6">
        <input type="text" wire:model.live="buscar" placeholder="Buscar por nombre o código"
               class="border rounded-2xl px-4 py-2 w-full sm:w-1/3 focus:ring-2 focus:ring-blue-500 transition">
        <select wire:model.live="filtroEstado"
                class="border rounded-2xl px-4 py-2 w-full sm:w-1/3 focus:ring-2 focus:ring-blue-500 transition">
            <option value="">Todos los estados</option>
            <option value="disponible">Disponibles</option>
            <option value="prestamo">En préstamo</option>
            <option value="fuera_servicio">Fuera de servicio</option>
        </select>
    </div>

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
                <div class="text-sm text-gray-500 font-semibold">Stock actual de baterías</div>
                <div class="mt-1">
                    <span class="px-3 py-2 rounded-full text-lg font-bold {{ $colorBadge }}">
                        {{ $stockActual }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Editor --}}
        <div class="flex items-end gap-3 flex-wrap">
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-600 mb-1">Modificar stock</label>
                <input type="number" wire:model.defer="nuevoStockBaterias"
                       step="1"
                       class="w-32 px-3 py-2 text-sm border border-gray-300 rounded-lg 
                              focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <button wire:click="guardarStockBaterias"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm 
                           px-4 py-2 rounded-lg shadow-sm transition">
                Guardar
            </button>
        </div>
    </div>

    <div class="flex flex-wrap gap-4 mb-6">
        <button wire:click="abrirModalPrestamoMultiple"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-2xl font-semibold">
            Préstamo múltiple
        </button>
        <button wire:click="abrirModalDevolucionMultiple"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-2xl font-semibold">
            Devolución múltiple
        </button>
    </div>
<div class="block sm:hidden space-y-4">
    @forelse($herramientas as $herramienta)
        <div class="bg-white shadow-lg rounded-2xl p-4 border border-gray-100">

            <!-- Header -->
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">
                        {{ $herramienta->nombre }}
                    </h3>
                    <p class="text-xs text-gray-500 font-mono">
                        Código: {{ $herramienta->codigo }}
                    </p>
                    <p class="text-xs text-gray-500 font-mono">
                        GCI: {{ $herramienta->gci_codigo }}
                    </p>
                </div>

                <div class="text-right">
                    <div class="text-2xl font-black text-gray-800">
                        {{ $herramienta->cantidad }}
                    </div>
                    <span class="text-xs text-gray-400 uppercase">Total</span>
                </div>
            </div>

            <!-- Alimentación -->
            <div class="mt-3">
                @if($herramienta->tipo_alimentacion === 'bateria')
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Batería</span>
                @elseif($herramienta->tipo_alimentacion === 'cable')
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Cable</span>
                @else
                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">Manual</span>
                @endif
            </div>

            <!-- Estado -->
            <div class="mt-4 space-y-2 text-sm">

                <div class="text-green-600 font-semibold">
                    Disponible: {{ $herramienta->cantidad_disponible }}
                </div>

                <!-- Préstamo herramientas -->
                <button wire:click="togglePrestamos({{ $herramienta->id }})"
                        class="w-full text-left bg-blue-50 rounded-lg px-3 py-2 text-blue-700 font-medium">
                    En préstamo (herramientas): {{ $herramienta->cantidad_prestamo }}
                </button>

                <!-- Préstamo baterías -->
                @php $bateriasActivas = $herramienta->prestamos->where('estado','prestada')->sum('cantidad_baterias'); @endphp
                <button wire:click="togglePrestamosBaterias({{ $herramienta->id }})"
                        class="w-full text-left bg-green-50 rounded-lg px-3 py-2 text-green-700 font-medium">
                    En préstamo (baterías): {{ $bateriasActivas }}
                </button>

                <!-- Fuera servicio -->
                <button wire:click="toggleFueraServicio({{ $herramienta->id }})"
                        class="w-full text-left bg-red-50 rounded-lg px-3 py-2 text-red-700 font-medium">
                    Fuera de servicio: {{ $herramienta->cantidad_fuera_servicio }}
                </button>

            </div>

            <!-- Acciones -->
            <div class="mt-4 grid grid-cols-5 gap-2">

                <button wire:click="abrirModalPrestamo({{ $herramienta->id }})"
                        class="p-2 bg-green-100 text-green-700 rounded-xl text-xs font-semibold">
                    +
                </button>

                @php
                    $herramientasPendientes = $herramienta->cantidad_prestamo > 0;
                    $prestamosActivos = $herramienta->prestamos->where('estado', 'prestada');
                    $bateriasPendientes = $prestamosActivos->sum('cantidad_baterias');
                    $pendienteDevolver = $herramientasPendientes || $bateriasPendientes > 0;
                @endphp

                <button wire:click="abrirModalDevolver({{ $herramienta->id }})"
                        @disabled(!$pendienteDevolver)
                        class="p-2 bg-blue-100 text-blue-700 rounded-xl text-xs font-semibold disabled:opacity-30">
                    ↩
                </button>

                <button wire:click="abrirModalFueraServicio({{ $herramienta->id }})"
                        class="p-2 bg-red-100 text-red-700 rounded-xl text-xs font-semibold">
                    ✖
                </button>

                <button wire:click="abrirModalEditar({{ $herramienta->id }})"
                        class="p-2 bg-blue-100 text-blue-700 rounded-xl text-xs font-semibold">
                    ✎
                </button>

                <button wire:click="abrirModalEliminar({{ $herramienta->id }})"
                        class="p-2 bg-red-100 text-red-700 rounded-xl text-xs font-semibold">
                    🗑
                </button>

            </div>
        </div>
    @empty
        <div class="text-center text-gray-500 py-6">
            No hay herramientas
        </div>
    @endforelse
</div>


<div class="hidden sm:block bg-white shadow rounded-xl overflow-x-auto">
    @if(count($seleccionados) > 0)
    <button wire:click="eliminarSeleccionados"
            class="mb-4 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white transition-all px-4 py-2 rounded-xl shadow font-semibold">
        Eliminar seleccionados ({{ count($seleccionados) }})
    </button>
@endif
    <table class="w-full text-sm table-auto">
        <thead>

            <th class="p-3">
    <label class="flex items-center gap-3 cursor-pointer group select-none">
        
        <div class="relative flex items-center justify-center">
            <input type="checkbox"
                   wire:model.live="seleccionarTodos"
                   class="peer absolute opacity-0 w-5 h-5 cursor-pointer">

            <div class="w-5 h-5 rounded-full
                        border-2 border-gray-400
                        transition-all duration-150
                        group-hover:border-blue-600
                        peer-checked:bg-blue-600
                        peer-checked:border-blue-600
                        peer-checked:[&>svg]:opacity-100
                        flex items-center justify-center">

                <svg class="w-3.5 h-3.5 text-white opacity-0 transition-opacity"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="3.5"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 group-hover:text-gray-700 transition-colors">
            Seleccionar todo
        </span>

    </label>
</th>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Nombre</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Código</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">GCI Código</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Alimentación</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Cantidad Total</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($herramientas as $herramienta)
                <tr 
    wire:key="herramienta-{{ $herramienta->id }}"
    class="transition-colors
           {{ in_array($herramienta->id, $seleccionados) 
              ? 'bg-blue-50 border-l-4 border-blue-500' 
              : 'hover:bg-blue-50/40' }}">
                    <td class="p-3 text-center font-bold text-gray-800">{{ $herramienta->nombre }}</td>
                    <td class="p-3 text-center font-mono text-gray-600">{{ $herramienta->codigo }}</td>
                    <td class="p-3 text-center font-mono text-gray-600">{{ $herramienta->gci_codigo }}</td>
                    <td class="p-3 text-center">
                        @if($herramienta->tipo_alimentacion === 'bateria')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-sm font-semibold">Batería</span>
                        @elseif($herramienta->tipo_alimentacion === 'cable')
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-sm font-semibold">Cable</span>
                        @else
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-sm">Manual</span>
                        @endif
                    </td>
                    <td class="p-3 text-center font-black text-lg">{{ $herramienta->cantidad }}</td>
                    <td class="p-3 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="text-green-600 font-semibold">Disponible: {{ $herramienta->cantidad_disponible }}</div>
                            
                            {{-- En préstamo --}}
                            <div class="text-blue-600 w-full text-center">
                                <button wire:click="togglePrestamos({{ $herramienta->id }})" 
                                        class="flex justify-center items-center gap-1 w-full rounded-lg px-2 py-1 bg-blue-50 hover:bg-blue-100 transition">
                                    En préstamo (herramientas): {{ $herramienta->cantidad_prestamo }}
                                    <span>@if(isset($mostrarPrestamos[$herramienta->id])) &#9650; @else &#9660; @endif</span>
                                </button>
                            </div>

                            {{-- En préstamo baterías --}}
                            <div class="text-green-600 w-full text-center">
                                <button wire:click="togglePrestamosBaterias({{ $herramienta->id }})" 
                                        class="flex justify-center items-center gap-1 w-full rounded-lg px-2 py-1 bg-green-50 hover:bg-green-100 transition">
                                    @php $bateriasActivas = $herramienta->prestamos->where('estado','prestada')->sum('cantidad_baterias'); @endphp
                                    En préstamo (baterías): {{ $bateriasActivas }}
                                    <span>@if(isset($mostrarPrestamosBaterias[$herramienta->id])) &#9650; @else &#9660; @endif</span>
                                </button>
                            </div>

                            {{-- Fuera de servicio --}}
                            <div class="text-red-600 w-full text-center">
                                <button wire:click="toggleFueraServicio({{ $herramienta->id }})"
                                        class="flex justify-center items-center gap-1 w-full rounded-lg px-2 py-1 bg-red-50 hover:bg-red-100 transition">
                                    Fuera de servicio: {{ $herramienta->cantidad_fuera_servicio }}
                                    <span>@if(isset($mostrarFueraServicio[$herramienta->id])) &#9650; @else &#9660; @endif</span>
                                </button>
                            </div>
                        </div>
                    </td>

                    <!-- Acciones -->
                    <td class="p-4 text-right">
                        <div class="inline-flex justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity flex-wrap">
                            <!-- Prestar -->
                            <button title="Prestar" wire:click="abrirModalPrestamo({{ $herramienta->id }})"
                                    class="p-2 bg-green-50 text-green-700 rounded-xl hover:bg-green-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Devolver -->
                            @php
                                $herramientasPendientes = $herramienta->cantidad_prestamo > 0;
                                $prestamosActivos = $herramienta->prestamos->where('estado', 'prestada');
                                $bateriasPendientes = $prestamosActivos->sum('cantidad_baterias');
                                $pendienteDevolver = $herramientasPendientes || $bateriasPendientes > 0;
                            @endphp
                            <button title="Devolver" wire:click="abrirModalDevolver({{ $herramienta->id }})"
                                    @disabled(!$pendienteDevolver)
                                    class="p-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center disabled:opacity-30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 12h18M12 3l9 9-9 9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Fuera de servicio -->
                            <button title="Fuera de servicio" wire:click="abrirModalFueraServicio({{ $herramienta->id }})"
                                    @disabled($herramienta->estado == 'fuera_servicio')
                                    class="p-2 bg-red-50 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition-all flex items-center disabled:opacity-30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Editar -->
                            <button title="Editar" wire:click="abrirModalEditar({{ $herramienta->id }})"
                                    class="p-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Eliminar -->
                            <button title="Eliminar" wire:click="abrirModalEliminar({{ $herramienta->id }})"
                                    class="p-2 bg-red-50 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </td>
  <td class="p-3 text-center">
    <label class="relative flex justify-center items-center cursor-pointer group">
        
        <input type="checkbox"
               value="{{ $herramienta->id }}"
               wire:model.live="seleccionados"
               class="peer absolute opacity-0 w-5 h-5 cursor-pointer">

        <div class="w-5 h-5 rounded-full border-2 border-gray-400
                    flex items-center justify-center transition-all duration-150
                    group-hover:border-blue-600
                    peer-checked:bg-blue-600 
                    peer-checked:border-blue-600
                    peer-checked:[&>svg]:opacity-100"> <svg class="w-3.5 h-3.5 text-white opacity-0 transition-opacity"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="3.5"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>

        </div>
    </label>
</td> 
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center p-6 text-gray-500">No hay herramientas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

   <!-- ========================== BOTONES PRINCIPALES ========================== -->
<div class="flex flex-col md:flex-row md:gap-4 w-full">

    <!-- Botón Estadísticas -->
    <button wire:click="toggleEstadisticas"
            class="flex items-center justify-between bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow mb-2 md:mb-0 w-full md:w-auto transition-all">
        <span class="font-semibold text-base md:text-lg">Estadísticas de Herramientas</span>
        <svg class="w-5 h-5 transition-transform duration-300 {{ $estadisticasAbiertas ? 'rotate-180' : '' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Botón Historial -->
    <button wire:click="toggleHistorial"
            class="flex items-center justify-between bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow w-full md:w-auto transition-all">
        <span class="font-semibold text-base md:text-lg">Historial de Herramientas</span>
        <svg class="w-5 h-5 transition-transform duration-300 {{ $historialAbierto ? 'rotate-180' : '' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>
</div>

<!-- ========================== ESTADÍSTICAS ========================== -->
@if($estadisticasAbiertas)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mt-4">
    <h2 class="text-lg font-bold mb-4">Estadísticas de uso de herramientas</h2>

    <div class="flex flex-col sm:flex-row sm:gap-4 mb-4 flex-wrap">
        <button wire:click="exportarEstadisticasHerramientasCsv"
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg mb-2 sm:mb-0 transition-colors">
            Exportar CSV
        </button>
        <button wire:click="exportarEstadisticasHerramientasPdf"
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
            Exportar PDF
        </button>
    </div>

    <div class="overflow-x-auto rounded-lg border shadow-sm">
        <table class="w-full text-sm min-w-[500px]">
            <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th class="text-left p-3">Herramientas más utilizadas</th>
                <th class="text-left p-3">Herramientas menos utilizadas</th>
                <th class="text-left p-3">Funcionarios que más usan</th>
            </tr>
            </thead>
            <tbody>
            @php
                $max = max($herramientasMasUsadas->count(), $herramientasMenosUsadas->count(), $funcionariosUso->count());
            @endphp
            @for($i=0; $i<$max; $i++)
            <tr class="border-b hover:bg-blue-50 transition-colors">
                <td class="p-3">
                    @isset($herramientasMasUsadas[$i])
                        <div class="font-semibold">{{ $herramientasMasUsadas[$i]->nombre }}</div>
                        <div class="text-xs text-gray-500">{{ $herramientasMasUsadas[$i]->total_usos }} usos</div>
                    @endisset
                </td>
                <td class="p-3">
                    @isset($herramientasMenosUsadas[$i])
                        <div class="font-semibold">{{ $herramientasMenosUsadas[$i]->nombre }}</div>
                        <div class="text-xs text-gray-500">{{ $herramientasMenosUsadas[$i]->total_usos }} usos</div>
                    @endisset
                </td>
                <td class="p-3">
                    @isset($funcionariosUso[$i])
                        <div class="font-semibold">{{ $funcionariosUso[$i]->funcionario }}</div>
                        <div class="text-xs text-gray-500">{{ $funcionariosUso[$i]->total }} usos</div>
                    @endisset
                </td>
            </tr>
            @endfor
            @if($max === 0)
            <tr>
                <td colspan="3" class="text-center p-6 text-gray-500">No hay estadísticas registradas todavía</td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- ========================== HISTORIAL ========================== -->
@if($historialAbierto)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mt-4">
    <!-- FILTROS RESPONSIVE -->
    <div class="flex flex-wrap gap-4 mb-4 items-end">
        <div>
            <label class="text-sm font-semibold">Desde:</label>
            <input type="date" wire:model.live="filtroDesde" class="border rounded p-2 w-full min-w-[150px]">
        </div>
        <div>
            <label class="text-sm font-semibold">Hasta:</label>
            <input type="date" wire:model.live="filtroHasta" class="border rounded p-2 w-full min-w-[150px]">
        </div>
        <div>
            <label class="text-sm font-semibold">Tipo:</label>
            <select wire:model.live="filtroTipo" class="border rounded p-2 w-full min-w-[150px]">
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
                   placeholder="Buscar nombre o código" class="border rounded p-2 w-full">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="text-sm font-semibold">Funcionario:</label>
            <input type="text" wire:model.live="filtroFuncionario"
                   placeholder="Buscar funcionario" class="border rounded p-2 w-full">
        </div>
    </div>

    <!-- BOTONES EXPORTACIÓN -->
    <div class="flex flex-col sm:flex-row sm:gap-4 mb-4 flex-wrap">
        <button wire:click="exportarHistorialHerramientasCsv"
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg mb-2 sm:mb-0">
            Exportar CSV
        </button>
        <button wire:click="exportarHistorialHerramientasPdf"
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg">
            Exportar PDF
        </button>
    </div>

    <!-- TABLA RESPONSIVE -->
    <div class="overflow-x-auto rounded-lg border shadow-sm">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th class="text-left p-3">Fecha</th>
                <th class="text-left p-3">Herramienta</th>
                <th class="text-left p-3">Tipo</th>
                <th class="px-3 py-2 text-center">Baterías</th>
                <th class="text-left p-3">Funcionario</th>
                <th class="text-left p-3">Detalle</th>
            </tr>
            </thead>
            <tbody>
            @forelse($historial as $item)
            <tr class="border-b hover:bg-blue-50 transition-colors">
                <td class="p-3">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">{{ $item->nombre }}<br><span class="text-xs text-gray-500">{{ $item->codigo }}</span></td>
                <td class="p-3 capitalize">{{ str_replace('_',' ', $item->tipo) }}</td>
                <td class="px-3 py-2 text-center">
                    @if($item->cantidad_baterias > 0)
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">{{ $item->cantidad_baterias }}</span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-3">{{ $item->funcionario ?? 'N/A' }}</td>
                <td class="p-3 text-gray-600">{{ $item->detalle }}
                    @if($item->observacion)<div class="text-xs text-gray-500 mt-1">Obs: {{ $item->observacion }}</div>@endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center p-6 text-gray-500">No hay movimientos registrados</td></tr>
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
