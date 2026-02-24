<div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-4">

    {{-- ALERTA ÉXITO --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    @php
        $coloresEstado = [
            'pendiente' => 'text-yellow-500 font-bold',
            'aprobado' => 'text-green-500 font-bold',
            'rechazado' => 'text-red-500 font-bold',
        ];
    @endphp

    <!-- Header + Botón crear -->
    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
        <h2 class="text-2xl font-bold">Gestión de Pedidos</h2>
        <button
            wire:click="$set('mostrarModalCrear', true)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
            + Nuevo pedido
        </button>
    </div>

    <!-- Exportar -->
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach(['pendiente','completado','rechazado'] as $estado)
            <button wire:click="exportarPedidosCsv('{{ $estado }}')" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                Exportar {{ ucfirst($estado) }} CSV
            </button>
            <button wire:click="exportarPedidosPdf('{{ $estado }}')" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                Exportar {{ ucfirst($estado) }} PDF
            </button>
        @endforeach
    </div>

    <!-- Modal Crear Pedido -->
    <div 
        style="display: {{ $mostrarModalCrear ? 'flex' : 'none' }};"
        class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">

            <h2 class="text-lg font-bold mb-4">Crear Pedido</h2>

            <!-- Tipo de Pedido -->
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Tipo de Pedido</label>
                <select wire:model.live="tipoPedido" class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="herramienta_existente">Herramienta Existente</option>
                    <option value="herramienta_nueva">Herramienta Nueva</option>
                    <option value="material_existente">Material Existente</option>
                    <option value="material_nuevo">Material Nuevo</option>
                </select>
            </div>

            <!-- Herramienta existente -->
            @if($tipoPedido === 'herramienta_existente')
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Elegir Herramienta</label>
                    <select wire:model.live="herramientaSeleccionadaId" class="w-full border rounded p-2">
                        <option value="">Seleccionar herramienta</option>
                        @foreach($herramientas as $herr)
                            <option value="{{ $herr->id }}">{{ $herr->nombre }} (Disponible: {{ $herr->cantidad_disponible }})</option>
                        @endforeach
                    </select>
                    @if($herramientaSeleccionadaId)
                        <label class="block mt-2 font-medium">Cantidad a pedir</label>
                        <input type="number" min="1" placeholder="Cantidad a pedir" wire:model="cantidadNuevo" class="w-full mt-2 border p-2 rounded">
                        <input type="text" placeholder="SKU (opcional)" wire:model="skuNuevo" class="w-full mt-2 border p-2 rounded">
                    @endif
                </div>

            <!-- Material existente -->
            @elseif($tipoPedido === 'material_existente')
                <div class="mb-4">
                    <label class="block mb-1 font-semibold">Elegir Material</label>
                    <select wire:model.live="materialSeleccionadoId" class="w-full border rounded p-2">
                        <option value="">Seleccionar material</option>
                        @foreach($materiales as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->nombre }} (Stock: {{ $mat->stock_actual }})</option>
                        @endforeach
                    </select>
                    @if($materialSeleccionadoId)
                        <label class="block mt-2 font-medium">Cantidad a pedir</label>
                        <input type="number" min="1" placeholder="Cantidad a pedir" wire:model="cantidadNuevo" class="w-full mt-2 border p-2 rounded">
                        <input type="text" placeholder="SKU (opcional)" wire:model="skuNuevo" class="w-full mt-2 border p-2 rounded">
                    @endif
                </div>

            <!-- Nuevo -->
            @else
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium">Nombre *</label>
                        <input type="text" wire:model="nombreNuevo" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('nombreNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Código</label>
                        <input type="text" wire:model="codigoNuevo" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('codigoNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Cantidad a pedir *</label>
                        <input type="number" min="1" wire:model="cantidadNuevo" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('cantidadNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    @if($tipoPedido === 'material_nuevo')
                        <div>
                            <label class="block text-sm font-medium">Stock Mínimo *</label>
                            <input type="number" wire:model="stockMinimoNuevo" min="0" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('stockMinimoNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                    @endif

                    @if(in_array($tipoPedido, ['material_nuevo','herramienta_nueva']))
                        <div>
                            <label class="block text-sm font-medium">Código GCI</label>
                            <input type="text" wire:model="gciNuevo" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('gciNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                    @endif

                    @if($tipoPedido === 'herramienta_nueva')
                        <div>
                            <label class="block text-sm font-medium">Tipo de herramienta *</label>
                            <select wire:model="tipoHerramientaNuevo" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="cable">Cable</option>
                                <option value="bateria">Batería</option>
                                <option value="no_aplica">No aplica</option>
                            </select>
                            @error('tipoHerramientaNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium">SKU (opcional)</label>
                        <input type="text" wire:model="skuNuevo" class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('skuNuevo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                </div>
            @endif

            <div class="flex flex-wrap justify-end gap-2 mt-4">
                <button wire:click="$set('mostrarModalCrear', false)" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancelar</button>
                <button wire:click.prevent="guardarPedido" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">Guardar</button>
            </div>
        </div>
    </div>

<div class="hidden md:block bg-white shadow-xl shadow-gray-200/50 rounded-3xl overflow-hidden border border-gray-100">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Fecha</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Item</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">Código</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest">SKU</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Seguimiento</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Cantidad</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                <th class="p-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-50">
            @forelse($pedidos as $pedido)
                <tr class="hover:bg-blue-50/40 transition-colors group">
                    <!-- Fecha -->
                    <td class="p-4 font-mono text-gray-600">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>

                    <!-- Item -->
                    <td class="p-4 font-bold text-gray-800">{{ $pedido->nombre }}</td>

                    <!-- Código -->
                    <td class="p-4 font-mono text-gray-600">{{ $pedido->codigo ?? '---' }}</td>

                    <!-- SKU -->
                    <td class="p-4 font-mono text-gray-600">{{ $pedido->sku ?? '---' }}</td>

                    <!-- Seguimiento -->
                    <td class="p-4 text-center text-gray-600">{{ $pedido->numero_seguimiento ?? '---' }}</td>

                    <!-- Cantidad -->
                    <td class="p-4 text-center font-black">{{ $pedido->cantidad }}</td>

                    <!-- Estado -->
                    <td class="p-4 text-center">
                        @php
                            $estadoClase = match(strtolower($pedido->estado)) {
                                'pendiente' => 'bg-orange-100 text-orange-700',
                                'completado' => 'bg-green-100 text-green-700',
                                'rechazado' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            $estadoPing = match(strtolower($pedido->estado)) {
                                'pendiente' => 'bg-orange-500 animate-ping',
                                'completado' => 'bg-green-500',
                                'rechazado' => 'bg-red-500',
                                default => 'bg-gray-500'
                            };
                            $estadoTexto = ucfirst($pedido->estado);
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-tighter {{ $estadoClase }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $estadoPing }}"></span>
                            {{ $estadoTexto }}
                        </span>
                    </td>

                    <!-- Acciones -->
                    <td class="p-4 text-right"> <!-- Aquí es la clave -->
                        <div class="inline-flex justify-end gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity flex-wrap">

                            <!-- Completar -->
                            <button title="Completar" wire:click="completarPedido({{ $pedido->id }})"
                                class="p-2 bg-green-50 text-green-700 rounded-xl hover:bg-green-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Rechazar -->
                            <button title="Rechazar" wire:click="confirmarRechazo({{ $pedido->id }})"
                                class="p-2 bg-red-50 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <!-- Email -->
                            <button title="Enviar Email" wire:click="abrirModalMail({{ $pedido->id }})"
                                class="p-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-12 text-center text-gray-400">
                        <div class="flex flex-col items-center">
                            <svg class="h-10 w-10 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <span class="font-medium uppercase tracking-widest text-xs">No hay pedidos registrados</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Botón Toggle General --}}
<div class="mt-6">
    <button 
        wire:click="$toggle('mostrarHistorial')"
        class="px-4 py-2 rounded bg-blue-600 text-white  font-semibold">
        {{ $mostrarHistorial ? 'Ocultar Historial de Pedidos' : 'Ver Historial de Pedidos' }}
    </button>
</div>

@if($mostrarHistorial)
    {{-- BOTONES TOGGLE HISTORIAL --}}
    <div class="mt-4 flex gap-2">
        <button wire:click="$set('historialActivo', 'completados')"
            class="px-4 py-2 rounded {{ $historialActivo === 'completados' ? 'bg-blue-500 text-white font-semibold' : 'bg-gray-200' }}">
            Completados
        </button>

        <button wire:click="$set('historialActivo', 'rechazados')"
            class="px-4 py-2 rounded {{ $historialActivo === 'rechazados' ? 'bg-blue-500 text-white font-semibold' : 'bg-gray-200' }}">
            Rechazados
        </button>
    </div>

    {{-- TODO EL BLOQUE DE HISTORIAL (tablas de completados/rechazados) --}}
    <div class="mt-4">
        <div class="flex gap-2 mb-4">
    <input type="text" placeholder="Buscar por nombre" 
        class="border p-2 rounded"
        wire:model.live="filtroNombre">

    <input type="text" placeholder="Buscar N° Seguimiento" 
        class="border p-2 rounded"
        wire:model.live="filtroSeguimiento">
</div>
{{-- HISTORIAL --}}
<div class="mt-4">
    @if($historialActivo === 'completados')
        {{-- Herramientas Completadas --}}
        <h3 class="text-xl font-semibold mt-4 mb-2">Herramientas</h3>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm text-left border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border-b">Fecha Completado</th>
                        <th class="p-3 border-b">Material</th>
                        <th class="p-3 border-b">Cantidad</th>
                        <th class="p-3 border-b">N° Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosHerramientasCompletados as $pedido)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $pedido->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $pedido->nombre }}</td>
                            <td class="p-3">{{ $pedido->cantidad }}</td>
                            <td class="p-3">{{ $pedido->numero_seguimiento ?? '---' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-3 text-gray-500">No hay herramientas </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Materiales Completadas --}}
        <h3 class="text-xl font-semibold mt-4 mb-2">Materiales</h3>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm text-left border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border-b">Fecha Completado</th>
                        <th class="p-3 border-b">Material</th>
                        <th class="p-3 border-b">Cantidad</th>
                        <th class="p-3 border-b">N° Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosMaterialesCompletados as $pedido)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $pedido->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $pedido->nombre }}</td>
                            <td class="p-3">{{ $pedido->cantidad }}</td>
                            <td class="p-3">{{ $pedido->numero_seguimiento ?? '---' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-3 text-gray-500">No hay materiales</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @elseif($historialActivo === 'rechazados')
        {{-- Herramientas Rechazadas --}}
        <h3 class="text-xl font-semibold mt-4 mb-2">Herramientas Rechazadas</h3>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm text-left border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border-b">Fecha Rechazado</th>
                        <th class="p-3 border-b">Material</th>
                        <th class="p-3 border-b">Cantidad</th>
                        <th class="p-3 border-b">N° Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosHerramientasRechazados as $pedido)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $pedido->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $pedido->nombre }}</td>
                            <td class="p-3">{{ $pedido->cantidad }}</td>
                            <td class="p-3">{{ $pedido->numero_seguimiento ?? '---' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-3 text-gray-500">No hay herramientas rechazadas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Materiales Rechazadas --}}
        <h3 class="text-xl font-semibold mt-4 mb-2">Materiales Rechazadas</h3>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm text-left border border-gray-200 rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border-b">Fecha Rechazado</th>
                        <th class="p-3 border-b">Material</th>
                        <th class="p-3 border-b">Cantidad</th>
                        <th class="p-3 border-b">N° Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosMaterialesRechazados as $pedido)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $pedido->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $pedido->nombre }}</td>
                            <td class="p-3">{{ $pedido->cantidad }}</td>
                            <td class="p-3">{{ $pedido->numero_seguimiento ?? '---' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-3 text-gray-500">No hay materiales rechazados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
    </div>
@endif

@if($mostrarModalRechazo)
<div class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">
    <div class="bg-white rounded p-6 w-96">
        <h2 class="text-lg font-bold mb-4">Confirmar Rechazo</h2>
        <p class="mb-4">¿Estás seguro que deseas rechazar este pedido?</p>
        <div class="flex justify-end gap-2">
            <button wire:click="cancelarRechazo" 
                    class="px-4 py-2 rounded bg-gray-500  text-white">
                Cancelar
            </button>
            <button wire:click="rechazarPedido" 
                    class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                Rechazar
            </button>
        </div>
    </div>
</div>
@endif


<!-- Modal Mail -->

<div id="modal-mail" style="display: {{ $mostrarModalMail ? 'flex' : 'none' }};
            position: fixed; inset: 0; 
            background: rgba(0,0,0,0.5); 
            justify-content: center; 
            align-items: center; z-index: 50;">
    <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; width: 400px; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
        <h2 style="font-weight: bold; margin-bottom: 1rem;">Enviar pedido por correo</h2>
        <input 
            type="email" 
            id="emailDestino"
            placeholder="Correo electrónico" 
            style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #ccc; border-radius: 0.25rem;">
        <div style="display:flex; justify-content: flex-end; gap:0.5rem; margin-top:1rem;">
            <button onclick="document.getElementById('modal-mail').style.display='none';" 
                style="padding:0.5rem 1rem; border:none; background:#ccc; border-radius:0.25rem;">
                Cancelar
            </button>
            <button 
                onclick="
                    let email = document.getElementById('emailDestino').value;
                    if(email){
                        let pedido = {
                            nombre: '{{ $pedidoSeleccionadoId ? App\Models\Pedido::find($pedidoSeleccionadoId)->nombre : '' }}',
                            cantidad: '{{ $pedidoSeleccionadoId ? App\Models\Pedido::find($pedidoSeleccionadoId)->cantidad : '' }}',
                            seguimiento: '{{ $pedidoSeleccionadoId ? App\Models\Pedido::find($pedidoSeleccionadoId)->numero_seguimiento ?? 'N/A' : '' }}',
                            sku: '{{ $pedidoSeleccionadoId ? App\Models\Pedido::find($pedidoSeleccionadoId)->sku ?? 'N/A' : '' }}',
                            tipo: '{{ $pedidoSeleccionadoId ? App\Models\Pedido::find($pedidoSeleccionadoId)->tipo : '' }}'
                        };

                        let tipoTexto = pedido.tipo === 'herramienta' ? 'Herramienta' : 'Material';

                        let asunto = encodeURIComponent('Solicitud de Pedido: ' + pedido.nombre);

                        let cuerpo = encodeURIComponent(
`Hola,

Se solicita un pedido para la siguiente ${tipoTexto}:

- ${tipoTexto}: ${pedido.nombre}
- Cantidad: ${pedido.cantidad}
- N° de Seguimiento: ${pedido.seguimiento}
- SKU: ${pedido.sku}

Gracias.`
                        );

                        window.location.href = 'mailto:' + email + '?subject=' + asunto + '&body=' + cuerpo;
                        document.getElementById('modal-mail').style.display='none';
                    } else { 
                        alert('Ingrese un correo válido'); 
                    }
                " 
                style="padding:0.5rem 1rem; border:none; background:#1d4ed8; color:white; border-radius:0.25rem;">
                Enviar
            </button>
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
<script>
    window.addEventListener('abrir-mail', event => {
        const mailto = event.detail.mailto;
        if(mailto){
            window.location.href = mailto; // abre Outlook o cliente nativo
        }
        // Cerrar modal manualmente
        const modal = document.getElementById('modal-mail');
        if(modal){
            modal.style.display = 'none';
        }
    });
</script>


</div>
