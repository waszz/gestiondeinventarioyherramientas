<div class="bg-white shadow-xl rounded-2xl p-6 space-y-6">

    <h2 class="text-xl font-bold text-gray-800">Crear Ticket</h2>

    {{-- Usuario logueado --}}
    <div class="bg-gray-100 p-3 rounded-xl text-sm">
        Solicitado por: 
        <strong>{{ auth()->user()->name }}</strong>
    </div>

     <!-- Mensaje flash -->
      @if(session()->has('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-xl shadow">
            {{ session('success') }}
        </div>
    @endif


    {{-- Descripción --}}
    <textarea wire:model="descripcion"
              placeholder="Descripción del problema..."
              class="w-full border rounded-xl p-3"></textarea>

    @error('descripcion') 
        <span class="text-red-500 text-sm">{{ $message }}</span> 
    @enderror

    <div class="grid grid-cols-2 gap-4">
<div class="col-span-2 space-y-8">

    {{-- SECTOR --}}
    <div class="space-y-3">

        <div class="flex justify-between items-center">
            <label class="text-sm font-semibold text-gray-700">
                Sector
            </label>

            <button type="button"
                    wire:click="$toggle('mostrarCrearSector')"
                    class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                + Crear
            </button>
        </div>

        <select wire:model.live="sector_id" class="w-full border border-gray-300 rounded-xl p-2 text-sm 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
    <option value="">Seleccionar sector</option>

    @foreach($this->sectores as $sector)
        <option value="{{ $sector->id }}">
            {{ $sector->nombre }}
        </option>
    @endforeach
</select>

@if($sector_id)
    <button type="button"
            wire:click="$set('confirmandoEliminarSector', true)"
            class="text-xs text-red-600 hover:text-red-800 transition">
        Eliminar sector
    </button>
@endif

        @error('sector_id')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror

        @if($mostrarCrearSector)
            <div class="flex gap-2 items-center">
                <input type="text"
                       wire:model.defer="nuevoSector"
                       placeholder="Nombre del sector"
                       class="flex-1 border border-gray-300 rounded-xl p-2 text-sm
                              focus:ring-2 focus:ring-blue-500 focus:outline-none">

                <button type="button"
                        wire:click="crearSector"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm transition">
                    Guardar
                </button>
            </div>

            @error('nuevoSector')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        @endif

    </div>


    {{-- LUGAR ESPECÍFICO --}}
    <div class="space-y-3">

        <div class="flex justify-between items-center">
            <label class="text-sm font-semibold text-gray-700">
                Lugar específico
            </label>

            <button type="button"
                    wire:click="$toggle('mostrarCrearLugar')"
                    class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                + Crear
            </button>
        </div>

        <select wire:model.live="lugar_id"
                class="w-full border border-gray-300 rounded-xl p-2 text-sm 
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
            <option value="">Seleccionar lugar</option>
            @foreach($this->lugares as $lugar)
                <option value="{{ $lugar->id }}">
                    {{ $lugar->nombre }}
                </option>
            @endforeach
        </select>

  @if($lugar_id)
    <button type="button"
            wire:click="$set('confirmandoEliminarLugar', true)"
            class="text-xs text-red-600 hover:text-red-800 transition">
        Eliminar lugar
    </button>
@endif

        @error('lugar_id')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror

        @if($mostrarCrearLugar)
            <div class="flex gap-2 items-center">
                <input type="text"
                       wire:model.defer="nuevoLugar"
                       placeholder="Nombre del lugar"
                       class="flex-1 border border-gray-300 rounded-xl p-2 text-sm
                              focus:ring-2 focus:ring-blue-500 focus:outline-none">

                <button type="button"
                        wire:click="crearLugar"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm transition">
                    Guardar
                </button>
            </div>

            @error('nuevoLugar')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror
        @endif

    </div>

</div>
        <select wire:model.live="tipo_rotura"
        class="border rounded-xl p-2 w-full">
    <option value="">Seleccionar tipo de rotura</option>

    <option value="AIRE ACONDICIONADO">AIRE ACONDICIONADO</option>
    <option value="ALBAÑILERIA">ALBAÑILERIA</option>
    <option value="ASCENSOR">ASCENSOR</option>
    <option value="ESCALERA MECANICA">ESCALERA MECANICA</option>
    <option value="SALVA ESCALERA">SALVA ESCALERA</option>
    <option value="MONTACARGA">MONTACARGA</option>
    <option value="CALEFACCION">CALEFACCION</option>
    <option value="CAMA ELECTRICA">CAMA ELECTRICA</option>
    <option value="CAMA MECANICA">CAMA MECANICA</option>
    <option value="CARPINTERIA">CARPINTERIA</option>
    <option value="CARPINTERIA DE ALUMINIO">CARPINTERIA DE ALUMINIO</option>
    <option value="CERRAJERIA">CERRAJERIA</option>
    <option value="COMPUTOS APOYO A IT">COMPUTOS APOYO A IT</option>
    <option value="ELECTRICA">ELECTRICA</option>
    <option value="ELECTROMECANICA">ELECTROMECANICA</option>
    <option value="EQUIPAMIENTO">EQUIPAMIENTO</option>
    <option value="GASES MEDICINALES">GASES MEDICINALES</option>
    <option value="HERRERIA">HERRERIA</option>
    <option value="LIMPIEZA Y ORDEN">LIMPIEZA Y ORDEN</option>
    <option value="MAQUINAS">MAQUINAS</option>
    <option value="OFICINA">OFICINA</option>
    <option value="PAÑOL">PAÑOL</option>
    <option value="PEONES">PEONES</option>
    <option value="PINTURERIA">PINTURERIA</option>
    <option value="REPARACIONES GENERALES">REPARACIONES GENERALES</option>
    <option value="SANITARIA">SANITARIA</option>
    <option value="TERCERIZADO">TERCERIZADO</option>
    <option value="VIDRIERIA">VIDRIERIA</option>
</select>


        {{-- Prioridad --}}
        <select wire:model="prioridad"
                class="border rounded-xl p-2">
            <option value="baja">Baja</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
            <option value="urgente">Urgente</option>
        </select>

        {{-- Estado --}}
        <select wire:model="status"
                class="border rounded-xl p-2">
            <option value="abierto">Abierto</option>
            <option value="en_proceso">En proceso</option>
            <option value="cerrado">Cerrado</option>
        </select>

        {{-- Asignado a (Funcionarios reales) --}}
        <select wire:model="funcionario_id"
                class="border rounded-xl p-2">
            <option value="">Seleccionar funcionario</option>

            @foreach($funcionarios as $f)
                <option value="{{ $f->id }}">
                    {{ $f->nombre }} {{ $f->apellido }} - {{ $f->area }}
                </option>
            @endforeach
        </select>

       <select wire:model.live="categoria"
        class="border rounded-xl p-2 w-full">
    <option value="">Seleccionar categoría</option>

    <option value="ELECTROMECANICA">ELECTROMECANICA</option>
    <option value="ELIMINADA CON OBSERVACION">ELIMINADA CON OBSERVACION</option>
    <option value="EN PROCESO CON PENDIENTES">EN PROCESO CON PENDIENTES</option>
    <option value="EN PROCESO ESPERANDO MATERIAL">EN PROCESO ESPERANDO MATERIAL</option>
    <option value="ESPERANDO AUTORIZACION PARA EJECUCION">ESPERANDO AUTORIZACION PARA EJECUCION</option>
    <option value="ESPERANDO LIBERACION DE SALA">ESPERANDO LIBERACION DE SALA</option>
    <option value="NUEVA OBRA">NUEVA OBRA</option>
    <option value="TERCERIZADOS">TERCERIZADOS</option>
    <option value="TRABAJO DE MANTENIMIENTO">TRABAJO DE MANTENIMIENTO</option>
    <option value="TRABAJO DE MANTENIMIENTO CORRECTIVO">TRABAJO DE MANTENIMIENTO CORRECTIVO</option>
    <option value="TRABAJO DE MANTENIMIENTO PLANIFICADO">TRABAJO DE MANTENIMIENTO PLANIFICADO</option>
    <option value="TRABAJO MANTENIMIENTO PREVENTIVO">TRABAJO MANTENIMIENTO PREVENTIVO</option>
    <option value="TRABAJOS A REALIZAR">TRABAJOS A REALIZAR</option>
    <option value="TRAMITE">TRAMITE</option>
</select>

        <input type="text" wire:model="proyecto"
               placeholder="Proyecto"
               class="border rounded-xl p-2">

    </div>

    {{-- Detalles --}}
    <textarea wire:model="detalles"
              placeholder="Detalles adicionales..."
              class="w-full border rounded-xl p-3"></textarea>

    {{-- Botón --}}
    <button wire:click="guardar"
            class="bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 transition">
        Crear Ticket
    </button>
    <div class="mt-10 bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-100">

    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-lg font-bold">Lista de Tickets</h2>
    </div>
<div class="flex flex-wrap gap-4 mb-4">

    <!-- Buscar funcionario -->
    <input type="text"
        wire:model.live="buscarFuncionario"
        placeholder="Buscar funcionario..."
        class="border rounded-lg p-2">

    <!-- Tipo de rotura -->
    <select wire:model.live="filtroTipoRotura" class="border rounded-lg p-2">
        <option value="">Tipo de rotura</option>
        <option value="ELECTRICA">ELECTRICA</option>
        <option value="SANITARIA">SANITARIA</option>
        <option value="ALBAÑILERIA">ALBAÑILERIA</option>
        <option value="CARPINTERIA">CARPINTERIA</option>
        <option value="PINTURERIA">PINTURERIA</option>
        <option value="HERRERIA">HERRERIA</option>
    </select>

    <!-- Sector -->
    <select wire:model.live="filtroSector" class="border rounded-lg p-2">
        <option value="">Sector</option>
        @foreach($sectores as $sector)
            <option value="{{ $sector->id }}">{{ $sector->nombre }}</option>
        @endforeach
    </select>

    <!-- Lugar -->
    <select wire:model.live="filtroLugar" class="border rounded-lg p-2">
        <option value="">Lugar</option>
        @foreach($lugares as $lugar)
            <option value="{{ $lugar->id }}">{{ $lugar->nombre }}</option>
        @endforeach
    </select>
    
    <select wire:model.live="filtroEstadoFuncionario" class="border rounded-lg p-2">
    <option value="">Todos los estados</option>
    <option value="disponible">Disponible</option>
    <option value="no_disponible">No disponible</option>
</select>

</div>
  <div class="overflow-x-auto max-h-[600px] overflow-y-auto border rounded-xl">
    @if(count($seleccionados) > 0)
<div class="mb-3">
    <button wire:click="eliminarSeleccionados"
        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
        Eliminar seleccionados ({{ count($seleccionados) }})
    </button>
</div>
@endif
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-xs uppercase">

                
    <tr>
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

        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 
                     group-hover:text-gray-700 transition-colors">
            Seleccionar todo
        </span>

    </label>
</th>
        <th class="p-3">#</th>
        <th class="p-3">Descripción</th>
        <th class="p-3">Funcionario</th>
        <th class="p-3">Tipo de rotura</th>
        <th class="p-3">Categoría</th>
        <th class="p-3">Sector</th>
        <th class="p-3">Lugar</th>
        <th class="p-3">Prioridad</th>
        <th class="p-3">Estado</th>
        <th class="p-3">Fecha</th>
    </tr>
</thead>
            <tbody>
                @forelse($tickets as $ticket)

                   
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">
    <label class="relative flex justify-center items-center cursor-pointer group">

        <input type="checkbox"
               value="{{ $ticket->id }}"
               wire:model.live="seleccionados"
               class="peer absolute opacity-0 w-5 h-5 cursor-pointer">

        <div class="w-5 h-5 rounded-full border-2 border-gray-400
                    flex items-center justify-center transition-all duration-150
                    group-hover:border-blue-600
                    peer-checked:bg-blue-600 
                    peer-checked:border-blue-600
                    peer-checked:[&>svg]:opacity-100">

            <svg class="w-3.5 h-3.5 text-white opacity-0 transition-opacity"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="3.5"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M5 13l4 4L19 7"/>
            </svg>

        </div>
    </label>
</td>
                        <td class="p-3 font-bold">{{ $ticket->id }}</td>

                        
                        <td class="p-3">
                            {{ $ticket->descripcion }}
                        </td>
                        
                        <td class="p-3">
                            {{ $ticket->funcionario?->nombre ?? '-' }} {{ $ticket->funcionario?->apellido ?? '-' }}
                        </td>

                          <td class="p-3">
                                {{ $ticket->tipo_rotura ?? '-' }}
                            </td>

                            <td class="p-3">
                                {{ $ticket->categoria ?? '-' }}
                            </td>


                        <td class="p-3">
                          
                    {{ $sector?->nombre ?? 'Sin sector' }}
                        </td>

                        <td class="p-3">
                            {{ $ticket->lugar?->nombre ?? '-' }}
                        </td>

                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if($ticket->prioridad == 'alta') bg-red-100 text-red-700
                                @elseif($ticket->prioridad == 'media') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ ucfirst($ticket->prioridad) }}
                            </span>
                        </td>

                       <td class="p-3">
                            <span 
                                wire:click="cambiarEstado({{ $ticket->id }})"
                                class="px-3 py-1 rounded-full text-xs font-semibold cursor-pointer hover:opacity-80
                                @if($ticket->status == 'abierto') bg-blue-100 text-blue-700
                                @elseif($ticket->status == 'en_proceso') bg-orange-100 text-orange-700
                                @else bg-gray-200 text-gray-700
                                @endif">

                                {{ str_replace('_',' ', ucfirst($ticket->status)) }}

                            </span>
                        </td>

                        <td class="p-3">
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="p-4 text-center text-gray-400">
                            No hay tickets creados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
<livewire:seguimiento-funcionarios />

@if($confirmandoEliminarSector)
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-96 space-y-4 animate-fade-in">
        <h3 class="text-lg font-semibold text-gray-800">
            ¿Eliminar sector?
        </h3>

        <p class="text-sm text-gray-600">
            Esta acción no se puede deshacer.
        </p>

        <div class="flex justify-end gap-3 pt-4">
            <button wire:click="$set('confirmandoEliminarSector', false)"
                    class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-sm">
                Cancelar
            </button>

            <button wire:click="eliminarSector"
                    class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm">
                Eliminar
            </button>
        </div>
    </div>
</div>
@endif

@if($confirmandoEliminarLugar)
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-96 space-y-4">
        <h3 class="text-lg font-semibold text-gray-800">
            ¿Eliminar lugar?
        </h3>

        <p class="text-sm text-gray-600">
            Esta acción no se puede deshacer.
        </p>

        <div class="flex justify-end gap-3 pt-4">
            <button wire:click="$set('confirmandoEliminarLugar', false)"
                    class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-sm">
                Cancelar
            </button>

            <button wire:click="eliminarLugar"
                    class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm">
                Eliminar
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