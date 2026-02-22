<div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-4">
     {{-- ALERTA EXITO --}}
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
  <!-- Botón abrir modal -->
<div class="flex justify-between mb-4">
    <h2 class="text-2xl font-bold">Gestión de Pedidos</h2>

    <button
        wire:click="$set('mostrarModalCrear', true)"
        class="bg-blue-600 text-white px-3 py-2 rounded">
        + Nuevo pedido
    </button>
</div>

<div class="flex gap-2 mb-4">

    <button wire:click="exportarPedidosCsv('pendiente')"
        class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Exportar pendientes CSV
    </button>

    <button wire:click="exportarPedidosPdf('pendiente')"
        class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Exportar pendientes PDF
    </button>

    <button wire:click="exportarPedidosCsv('completado')"
        class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Exportar completados CSV
    </button>

    <button wire:click="exportarPedidosPdf('completado')"
        class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Exportar completados PDF
    </button>

    <button wire:click="exportarPedidosCsv('rechazado')"
        class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Exportar rechazados CSV
    </button>

    <button wire:click="exportarPedidosPdf('rechazado')"
        class="bg-gray-500 text-white px-4 py-2 rounded-lg">
        Exportar rechazados PDF
    </button>

</div>


<!-- Modal Crear Pedido -->
<div 
    style="display: {{ $mostrarModalCrear ? 'flex' : 'none' }};
           position: fixed; inset: 0; background: rgba(0,0,0,0.5);
           justify-content: center; align-items: center; z-index: 50;">
    <div class="bg-white p-6 rounded shadow w-96">
        <h2 class="font-bold mb-4">Crear Pedido</h2>

        <!-- Tipo de Pedido -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Tipo de Pedido</label>
            <select wire:model.live="tipoPedido" class="w-full border p-2 rounded">
                <option value="">Seleccionar...</option>
                <option value="herramienta_existente">Herramienta Existente</option>
                <option value="herramienta_nueva">Herramienta Nueva</option>
                <option value="material_existente">Material Existente</option>
                <option value="material_nuevo">Material Nuevo</option>
            </select>
        </div>

        <!-- Seleccionar existente -->
        @if($tipoPedido === 'herramienta_existente')
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Elegir Herramienta</label>
                <select wire:model.live="herramientaSeleccionadaId" class="w-full border p-2 rounded">
                    <option value="">Seleccionar herramienta</option>
                    @foreach($herramientas as $herr)
                        <option value="{{ $herr->id }}">{{ $herr->nombre }} (Disponible: {{ $herr->cantidad_disponible }})</option>
                    @endforeach
                </select>
                @if($herramientaSeleccionadaId)
                    <label class="block mt-2 font-medium">Cantidad a pedir</label>
<input 
    type="number" 
    min="1" 
    placeholder="Cantidad a pedir" 
    wire:model="cantidadNuevo" 
    class="w-full mt-2 border p-2 rounded"
/>
                    <input type="text" placeholder="SKU (opcional)" wire:model="skuNuevo" class="w-full mt-2 border p-2 rounded">
                @endif
            </div>
        @elseif($tipoPedido === 'material_existente')
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Elegir Material</label>
                <select wire:model.live="materialSeleccionadoId" class="w-full border p-2 rounded">
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
        @else
           <!-- Campos para nuevo -->
<div class="mb-4">
    <input type="text" placeholder="Nombre Nuevo" wire:model="nombreNuevo" class="w-full border p-2 rounded mb-2">

    <input type="text" placeholder="Código Nuevo" wire:model="codigoNuevo" class="w-full border p-2 rounded mb-2">

    <input type="number" min="1" placeholder="Cantidad a pedir" wire:model="cantidadNuevo" class="w-full border p-2 rounded mb-2">

<!-- SOLO PARA NUEVO GCI -->
@if(in_array($tipoPedido, ['material_nuevo', 'herramienta_nueva']))
    <input type="text"
           placeholder="GCI"
           wire:model="gciNuevo"
           class="w-full border p-2 rounded mb-2">
    @error('gciNuevo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
@endif
<!-- SOLO PARA NUEVA HERRAMIENTA -->
@if($tipoPedido === 'herramienta_nueva')
    <div class="mb-2">
        <label class="block mb-1 font-semibold">Tipo de herramienta</label>
        <select wire:model="tipoHerramientaNuevo" class="w-full border p-2 rounded">
            <option value="">Seleccionar...</option>
            <option value="cable">Cable</option>
            <option value="bateria">Batería</option>
            <option value="no_aplica">No aplica</option>
        </select>
        @error('tipoHerramientaNuevo') 
            <span class="text-red-500 text-sm">{{ $message }}</span> 
        @enderror
    </div>
@endif


    <input type="text" placeholder="SKU (opcional)" wire:model="skuNuevo" class="w-full border p-2 rounded">
</div>
        @endif

        <!-- Botones -->
        <div class="flex justify-end gap-2 mt-4">
            <button wire:click="$set('mostrarModalCrear', false)" class="bg-gray-500 text-white px-3 py-1 rounded">Cancelar</button>
            <button wire:click="guardarPedido" class="bg-blue-600 text-white px-3 py-1 rounded">Guardar</button>
        </div>
    </div>
</div>


    {{-- TABLA --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-3 text-left">Fecha</th>
                    <th class="p-3 text-left">Item</th>
                    <th class="p-3 text-left">Código</th>
                    <th class="p-3 text-left">SKU</th>
                    <th class="p-3 text-left">Seguimiento</th>
                    <th class="p-3 text-left">Cantidad</th>
                    <th class="p-3 text-left">Estado</th>
                    <th class="p-3 text-left">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pedidos as $pedido)
                    <tr class="border-b">

                        <td class="p-3">
                            {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="p-3 font-semibold">
                            {{ $pedido->nombre }}
                            <div class="text-xs text-gray-500 capitalize">
                                {{ $pedido->tipo }}
                            </div>
                        </td>

                        <td class="p-3">{{ $pedido->codigo }}</td>
                        <td class="p-3">{{ $pedido->sku }}</td>
                        <td class="p-3">{{ $pedido->numero_seguimiento ?? '---' }}</td>
                        <td class="p-3">{{ $pedido->cantidad }}</td>

                       <td class="p-3">
                        <span class="px-2 py-1 rounded text-white
                            {{ 
                                $pedido->estado == 'Pendiente' ? 'bg-orange-600 font-bold' : 
                                ($pedido->estado == 'Completado' ? 'bg-green-600 font-bold' : 
                                ($pedido->estado == 'Rechazado' ? 'bg-red-600 font-bold' : 'bg-orange-600')) 
                            }}
                        ">
                            {{ ucfirst($pedido->estado) }}
                        </span>
                    </td>

                        <td class="p-3 flex gap-2">

                           <button wire:click="completarPedido({{ $pedido->id }})" class="btn btn-success bg-green-600 text-white px-2 py-1 rounded">
                            
                                Completar
                            </button>

                          <button wire:click="confirmarRechazo({{ $pedido->id }})" 
                                class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                Rechazar
                            </button>

                            <button 
                                class="bg-blue-500 text-white px-2 py-1 rounded"
                                wire:click="abrirModalMail({{ $pedido->id }})">
                                Email
                            </button>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center p-6 text-gray-500">
                            No hay pedidos registrados
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
