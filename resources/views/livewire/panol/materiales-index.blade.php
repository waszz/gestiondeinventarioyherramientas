<div class="p-4 md:p-8 bg-gray-50 min-h-screen space-y-6">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Gestión de Materiales</h2>
            <p class="text-sm text-gray-500">Administra el inventario, controla stocks y gestiona pedidos.</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('panol.materiales.crear') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-sm font-bold transition-all active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Material
            </a>
            <div class="inline-flex shadow-sm rounded-xl overflow-hidden">
                <button wire:click="exportarCsv" class="bg-white border-y border-l border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 text-sm font-medium transition">CSV</button>
                <a href="{{ route('materiales.exportar.pdf') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 text-sm font-medium transition text-center">PDF</a>
            </div>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if($hayStockBajo)
        <div class="flex items-center p-4 text-red-800 rounded-2xl bg-red-50 border border-red-100 shadow-sm animate-pulse">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            <span class="text-sm font-bold tracking-wide uppercase">Alerta de Stock Crítico: Revisar inventario.</span>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-xl shadow">
            {{ session('success') }}
        </div>
    @endif

  {{-- BUSCADOR, IMPORTACIÓN Y FILTRO DE ESTADO --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    {{-- Buscador --}}
    <div class="lg:col-span-2 relative group">
        <input type="text" wire:model.live.debounce.300ms="buscar" 
            placeholder="Buscar por nombre o código..." 
            class="w-full pl-12 pr-4 py-3 bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-500 transition-all text-gray-700">
        <svg class="w-6 h-6 text-gray-400 absolute left-4 top-3 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>

    {{-- Filtro de estado --}}
    <div class="flex items-center bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <select wire:model.live="filtroEstado" class="w-full px-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500">
            <option value="">Todos los estados</option>
            <option value="critico">Crítico</option>
            <option value="optimo">Óptimo</option>
        </select>
    </div>

    {{-- Importación --}}
    <div class="flex bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <input type="file" wire:model="archivoImportacion" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-full">
        <button wire:click="importarMateriales" class="bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-black transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
        </button>
    </div>
</div>

{{-- CONFIGURACIÓN COLUMNAS --}} 
@if($columnasDetectadas) 
<div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 space-y-4"> 
    <h3 class="text-lg font-semibold mb-2">Configurar Columnas</h3> 
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> 
        @foreach(['Nombre'=>'columnaNombre','Código GCI'=>'columnaGci','Stock Actual'=>'columnaStock','Stock Mínimo'=>'columnaStockMinimo'] as $label => $model) 
        <div> 
            <label class="block text-sm font-medium mb-1">{{ $label }} <span class="text-red-500">*</span></label>
             <select wire:model="{{ $model }}" class="w-full border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                 <option value="">Seleccionar columna</option> @foreach($columnasDetectadas as $col) <option value="{{ $col }}">{{ $col }}</option>
                  @endforeach 
                </select> 
            </div> @endforeach 
        </div> @error('duplicado') <span class="text-red-500 text-sm block">{{ $message }}</span>
         @enderror <div class="flex justify-end mt-4"> 
            <button wire:click="importarMateriales" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow font-semibold transition"> Importar Materiales 
        </button> 
        </div> 
    </div>
     @endif

    {{-- VISTA MÓVIL (CARDS) --}}
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($materiales as $material)
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden">
                @if($material->stock_actual <= $material->stock_minimo)
                    <div class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase">Stock Bajo</div>
                @endif
                <div class="mb-4">
                    <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $material->nombre }}</h3>
                    <p class="text-xs text-gray-400 font-mono mt-1">{{ $material->codigo_referencia }} | GCI: {{ $material->gci_codigo ?? '---' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-5 border-y border-gray-50 py-3">
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold">Stock Actual</span>
                        <span class="text-xl font-black {{ $material->stock_actual <= $material->stock_minimo ? 'text-red-600' : 'text-gray-700' }}">{{ $material->stock_actual }}</span>
                    </div>
                    <div class="text-right">
                        <span class="block text-[10px] text-gray-400 uppercase font-bold">Mínimo</span>
                        <span class="text-xl font-black text-gray-400">{{ $material->stock_minimo }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="abrirModalIngreso({{ $material->id }})" class="flex-1 bg-green-600 text-white py-2 rounded-xl text-xs font-bold uppercase tracking-wider">Ingreso</button>
                    <button wire:click="abrirModalRetiro({{ $material->id }})" class="flex-1 bg-orange-500 text-white py-2 rounded-xl text-xs font-bold uppercase tracking-wider" {{ $material->stock_actual <= 0 ? 'disabled' : '' }}>Egreso</button>

                    @if($material->stock_actual <= $material->stock_minimo)
                        @php
                            $tienePedidoPendiente = \App\Models\Pedido::where('materiales_id', $material->id)->where('estado', 'pendiente')->exists();
                        @endphp
                        <button wire:click="abrirModalPedido({{ $material->id }})" @disabled($tienePedidoPendiente) class="flex-1 py-2 rounded-xl text-xs font-bold uppercase tracking-wider text-white {{ $tienePedidoPendiente ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                            {{ $tienePedidoPendiente ? 'Pedido enviado' : 'Realizar pedido' }}
                        </button>
                    @endif

                    <button wire:click="editarMaterial({{ $material->id }})" class="flex-1 bg-blue-500 text-white py-2 rounded-xl text-xs font-bold uppercase tracking-wider">Editar</button>
                    <button wire:click="abrirModalEliminarMaterial({{ $material->id }})" class="flex-1 bg-red-600 text-white py-2 rounded-xl text-xs font-bold uppercase tracking-wider">Eliminar</button>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 rounded-3xl border border-dashed border-gray-300 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <p class="mt-4 text-gray-500 font-medium">No hay materiales cargados en el sistema.</p>
            </div>
        @endforelse
    </div>

    {{-- VISTA ESCRITORIO (TABLA) --}}

<div class="hidden md:block bg-white shadow-xl shadow-gray-200/50 rounded-3xl overflow-hidden border border-gray-100">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Material</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Código</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">GCI</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Stock</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Mínimo</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($materiales as $material)
                <tr wire:key="material-row-{{ $material->id }}" class="hover:bg-blue-50/40 transition-colors group">
                    <td class="p-4 font-bold text-gray-800">{{ $material->nombre }}</td>
<td class="p-3">
    <select 
        wire:change="cambiarTipoMaterial({{ $material->id }}, $event.target.value)" 
        class="border rounded-xl px-3 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all bg-white hover:bg-gray-50"
    >
        @foreach($tiposMateriales as $tipo)
            <option 
                value="{{ $tipo }}" 
                {{ optional($material->tipo)->nombre === $tipo ? 'selected' : '' }}
            >
                {{ $tipo }}
            </option>
        @endforeach
    </select>
</td>
<td class="p-4 font-mono text-gray-600">{{ $material->codigo_referencia }}</td>
                    <td class="p-4 font-mono text-gray-600">{{ $material->gci_codigo ?? '---' }}</td>
                    <td class="p-4 text-center font-black {{ $material->stock_actual <= $material->stock_minimo ? 'text-red-600' : 'text-gray-700' }}">{{ $material->stock_actual }}</td>
                    <td class="p-4 text-center text-gray-400 font-bold">{{ $material->stock_minimo }}</td>
                    <td class="p-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-tighter {{ $material->stock_actual <= $material->stock_minimo ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $material->stock_actual <= $material->stock_minimo ? 'bg-red-500 animate-ping' : 'bg-green-500' }}"></span>
                            {{ $material->stock_actual <= $material->stock_minimo ? 'Crítico' : 'Óptimo' }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity flex-wrap">
                            <button title="Ingreso" wire:click="abrirModalIngreso({{ $material->id }})" class="p-2 bg-green-50 text-green-700 rounded-xl hover:bg-green-600 hover:text-white transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg></button>

                            <button title="Egreso" wire:click="abrirModalRetiro({{ $material->id }})" class="p-2 bg-orange-50 text-orange-700 rounded-xl hover:bg-orange-600 hover:text-white transition-all disabled:opacity-30" @disabled($material->stock_actual <= 0)><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 12H4"></path></svg></button>

                            @if($material->stock_actual <= $material->stock_minimo)
                                @php
                                    $tienePedidoPendiente = \App\Models\Pedido::where('materiales_id', $material->id)->where('estado', 'pendiente')->exists();
                                @endphp
                                <button title="Realizar pedido" 
                                    wire:click="abrirModalPedido({{ $material->id }})" 
                                    @disabled($tienePedidoPendiente) 
                                    class="p-2 rounded-xl text-white {{ $tienePedidoPendiente ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" class="w-5 h-5">
                                        <path d="M21 5L19 12H7.37671M20 16H8L6 3H3M11.5 7L13.5 9M13.5 9L15.5 7M13.5 9V3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            @endif

                            <button title="Editar" wire:click="editarMaterial({{ $material->id }})" class="p-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-600 hover:text-white transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>

                            <button title="Eliminar" wire:click="abrirModalEliminarMaterial({{ $material->id }})" class="p-2 bg-red-50 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-12 text-center text-gray-400">
                        <div class="flex flex-col items-center">
                            <svg class="h-10 w-10 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span class="font-medium uppercase tracking-widest text-xs">No hay materiales cargados</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>




  {{-- Modal Retiro --}}
@if($mostrarModalRetiro)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 md:p-8 animate-fade-in">
        <h2 class="text-xl md:text-2xl font-bold mb-6 text-gray-800">
            Retirar material: <span class="text-blue-600">{{ $materialSeleccionado->nombre }}</span>
        </h2>

        {{-- Inputs en grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Cantidad</label>
                <input type="number" wire:model="cantidadRetiro" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Destino</label>
                <input type="text" wire:model="destinoRetiro" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">N° Ticket</label>
                <input type="text" wire:model="ticketRetiro" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Funcionario</label>
                <select wire:model="funcionario_id" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
                    <option value="">Seleccionar</option>
                    @foreach($funcionarios as $funcionario)
                        <option value="{{ $funcionario->id }}">{{ $funcionario->nombre }} {{ $funcionario->apellido }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 mt-4 flex-wrap">
            <button wire:click="$set('mostrarModalRetiro', false)" class="flex-1 md:flex-none bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Cancelar
            </button>
            <button wire:click="guardarRetiro" class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Confirmar retiro
            </button>
        </div>
    </div>
</div>
@endif

{{-- Modal Ingreso --}}
@if($mostrarModalIngreso)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 md:p-8 animate-fade-in">
        <h2 class="text-xl md:text-2xl font-bold mb-6 text-gray-800">
            Ingreso de stock: <span class="text-blue-600">{{ $materialSeleccionado->nombre }}</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Stock</label>
                <input type="number" wire:model="stockIngreso" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>

            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Stock mínimo</label>
                <input type="number" wire:model="stockMinimoIngreso" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>
        </div>

        <div class="flex justify-end gap-3 flex-wrap">
            <button wire:click="$set('mostrarModalIngreso', false)" class="flex-1 md:flex-none bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Cancelar
            </button>
            <button wire:click="guardarIngreso" class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Confirmar ingreso
            </button>
        </div>
    </div>
</div>
@endif

{{-- Modal Editar --}}
@if($mostrarModalEditar)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 md:p-8 animate-fade-in">
        <h2 class="text-xl md:text-2xl font-bold mb-6 text-gray-800">Editar material</h2>

        <div class="grid grid-cols-1 gap-4 mb-6">
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Nombre</label>
                <input type="text" wire:model="nombreEditar" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700 mb-1 block">Código</label>
                <input type="text" wire:model="codigoEditar" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition">
            </div>
        </div>

        <div class="flex justify-end gap-3 flex-wrap">
            <button wire:click="$set('mostrarModalEditar', false)" class="flex-1 md:flex-none bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Cancelar
            </button>
            <button wire:click="guardarEdicionMaterial" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Guardar cambios
            </button>
        </div>
    </div>
</div>
@endif

{{-- Modal Eliminar --}}
@if($mostrarModalEliminarMaterial)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 md:p-8 animate-fade-in">
        <h2 class="text-xl md:text-2xl font-bold mb-4 text-red-600">Eliminar material</h2>
        <p class="mb-6 text-gray-700">¿Estás seguro de que deseas eliminar <strong>{{ $materialSeleccionado->nombre }}</strong>? Esta acción no se puede deshacer.</p>

        <div class="flex justify-end gap-3 flex-wrap">
            <button wire:click="$set('mostrarModalEliminarMaterial', false)" class="flex-1 md:flex-none bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Cancelar
            </button>
            <button wire:click="confirmarEliminarMaterial" class="flex-1 md:flex-none bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Eliminar
            </button>
        </div>
    </div>
</div>
@endif
{{-- Modal Pedido --}}
@if($mostrarModalPedido)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 md:p-8 animate-fade-in">
        <h2 class="text-xl md:text-2xl font-bold mb-6 text-gray-800">
            Realizar pedido de material
        </h2>

        <div class="grid grid-cols-1 gap-4 mb-6">
            {{-- Cantidad --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Cantidad a pedir
                </label>
                <input type="number"
                       wire:model="cantidadPedido"
                       min="1"
                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition"
                       placeholder="Ingresa la cantidad">
            </div>

            {{-- SKU --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    SKU (opcional)
                </label>
                <input type="text"
                       wire:model="skuPedido"
                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 transition"
                       placeholder="Código del material">
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 flex-wrap">
            <button wire:click="$set('mostrarModalPedido', false)"
                    class="flex-1 md:flex-none bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Cancelar
            </button>
            <button wire:click="guardarPedidoMaterial"
                    class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-xl transition-all">
                Enviar pedido
            </button>
        </div>
    </div>
</div>
@endif
<div class="p-6 mt-6 space-y-4">

    {{-- Toggle Estadísticas --}}
    <button wire:click="$toggle('mostrarEstadisticas')"
        class="w-full flex justify-between items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl shadow transition-all">
        <span class="font-semibold text-lg">Estadísticas de Materiales</span>
        <svg class="w-5 h-5 transition-transform duration-300 {{ $mostrarEstadisticas ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Contenedor Estadísticas --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded p-6 mt-2 transition-all duration-300 overflow-hidden"
         style="display: {{ $mostrarEstadisticas ? 'block' : 'none' }};">
        {{-- Contenido de estadísticas: filtros, exportación, tabla, etc. --}}
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
            <button wire:click="exportarEstadisticasCsv" class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
                Exportar CSV
            </button>
            <button wire:click="exportarEstadisticasPdf" class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
                Exportar PDF
            </button>
        </div>

        <div class="overflow-x-auto">
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
                        <tr class="border-b hover:bg-blue-50/20">
                            <td class="p-3 font-semibold">{{ $item->material->nombre ?? '---' }}</td>
                            <td class="p-3">{{ $item->material->codigo_referencia ?? '---' }}</td>
                            <td class="p-3 text-center font-bold">{{ $item->total_consumido }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center p-6 text-gray-500">No hay consumo registrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Toggle Historial --}}
    <button wire:click="$toggle('historialAbierto')"
        class="w-full flex justify-between items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl shadow transition-all">
        <span class="font-semibold text-lg">Historial de Movimientos</span>
        <svg class="w-5 h-5 transition-transform duration-300 {{ $historialAbierto ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Contenedor Historial --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded p-6 mt-2 transition-all duration-300 overflow-x-auto"
         style="display: {{ $historialAbierto ? 'block' : 'none' }};">
        {{-- Contenido de historial: filtros, exportación, tabla --}}
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
        </div>

        <div class="flex gap-2 mb-3">
            <button wire:click="exportarHistorialCsv" class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
                Exportar CSV
            </button>
            <a href="{{ route('historial.exportar') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg">
                Exportar PDF
            </a>
        </div>

        <div class="overflow-x-auto">
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
                        <tr class="border-b hover:bg-blue-50/20">
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
                            <td colspan="5" class="text-center p-6 text-gray-500">No hay movimientos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    window.addEventListener('reload-page', event => {
        // Espera 5 segundos (5000 ms) antes de recargar
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
</script>

</div>
