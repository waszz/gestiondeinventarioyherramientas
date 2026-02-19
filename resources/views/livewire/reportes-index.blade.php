<div>
    <!-- Tabs -->
    <div class="flex mb-4 space-x-2 border-b">
        <button wire:click="cambiarTab('materiales')" 
                class="px-4 py-2 font-bold text-white rounded-t-lg 
                       {{ $tab === 'materiales' ? 'bg-blue-600' : 'bg-gray-300' }}">
            Materiales
        </button>
        <button wire:click="cambiarTab('herramientas')" 
                class="px-4 py-2 font-bold text-white rounded-t-lg 
                       {{ $tab === 'herramientas' ? 'bg-blue-600' : 'bg-gray-300' }}">
            Herramientas
        </button>
    </div>

    <!-- Filtros + Acciones PDF en la misma fila -->
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <!-- Filtros -->
   

        <!-- Acciones PDF -->
        <a href="{{ route('reportes.pdf', $tab) }}" target="_blank" 
           class="bg-gray-500 text-white px-4 py-2 rounded-lg whitespace-nowrap">
            Imprimir
        </a>
        <button wire:click.prevent="descargarPDF" 
                class="bg-gray-500 text-white px-4 py-2 rounded-lg whitespace-nowrap">
         Exportar a PDF
        </button>
        {{-- <button wire:click="$set('mostrarModalEmail', true)" 
                class="bg-blue-500 text-white px-4 py-2 rounded whitespace-nowrap">
            Enviar por Email
        </button> --}}
    </div>

@if($tab === 'materiales')
    <table class="table-auto w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="px-4 py-2 border">Material</th>
                <th class="px-4 py-2 border">Código</th>
                <th class="px-4 py-2 border">Stock</th>
                <th class="px-4 py-2 border">Stock mínimo</th>
                <th class="px-4 py-2 border">Esencial</th>
                <th class="px-4 py-2 border">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materiales->filter(function($m) { 
                $estado = $this->filtroEstado ? ($m->estado ?? 'activo') === $this->filtroEstado : true;
                $esencial = $this->filtroEsencial 
                            ? ($m->material_esencial ? 'si' : 'no') === strtolower($this->filtroEsencial) 
                            : true;
                return $estado && $esencial;
            }) as $mat)
                <tr class="border-t">
                    <td class="px-4 py-2 border">{{ $mat->nombre }}</td>
                    <td class="px-4 py-2 border">{{ $mat->codigo_referencia }}</td>
                    <td class="px-4 py-2 border">{{ $mat->stock_actual }}</td>
                    <td class="px-4 py-2 border">{{ $mat->stock_minimo }}</td>
                    <td class="px-4 py-2 border">{{ $mat->material_esencial ? 'Sí' : 'No' }}</td>
                    
                    <!-- ESTADO CALCULADO POR STOCK -->
                    <td class="px-4 py-2 border">
                        @if($mat->stock_actual <= $mat->stock_minimo)
                            <span class="text-red-600 font-bold">Stock bajo</span>
                        @else
                            <span class="text-green-600 font-bold">Ok</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <table class="table-auto w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="px-4 py-2 border">Herramienta</th>
                <th class="px-4 py-2 border">Código</th>
                <th class="px-4 py-2 border">Disponibles</th>
                <th class="px-4 py-2 border">En préstamo</th>
                <th class="px-4 py-2 border">Fuera de servicio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($herramientas as $herr)
                <tr class="border-t">
                    <td class="px-4 py-2 border">{{ $herr->nombre }}</td>
                    <td class="px-4 py-2 border">{{ $herr->codigo }}</td>
                    <td class="px-4 py-2 border">{{ $herr->cantidad_disponible }}</td>
                    <td class="px-4 py-2 border">{{ $herr->cantidad_prestamo }}</td>
                    <td class="px-4 py-2 border">{{ $herr->cantidad_fuera_servicio }}</td>

                 
                </tr>
            @endforeach
        </tbody>
    </table>
@endif



    {{-- <!-- Modal email -->
    @if($mostrarModalEmail)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded w-96">
                <h3 class="text-lg font-bold mb-2">Enviar reporte por email</h3>
                <input type="email" wire:model="emailDestino" placeholder="Email destino" class="w-full mb-2 border p-2 rounded">
                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('mostrarModalEmail', false)" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                    <button wire:click="enviarEmail" class="px-4 py-2 bg-blue-600 text-white rounded">Enviar</button>
                </div>
            </div>
        </div>
    @endif
</div> --}}
