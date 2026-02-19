<div  class="max-w-7xl mx-auto p-4 space-y-6">
    @if(session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded text-center">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filtros y tabla de funcionarios -->
    <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
        <h3 class="font-semibold mb-4 text-center text-gray-800 dark:text-gray-200">
            Filtros / Selección de Funcionario
        </h3>

        <!-- Filtros -->
        <div  class="flex flex-wrap justify-center gap-4 mb-4">
            <input type="text" 
                   wire:model.live="numero_funcionario" 
                   placeholder="Buscar por N°, Nombre o Apellido"
                   class="p-2 border rounded dark:bg-gray-700 dark:text-white">

            <select wire:model.live="empresa" class="p-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="">Empresa</option>
                <option value="Gente">Gente</option>
                <option value="Prosepri">Prosepri</option>
                <option value="Asoc.Española">Asoc.Española</option>
            </select>

            <select wire:model.live="area" class="p-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="">Área</option>
                <option value="Reparaciones Generales">Rep. Grales</option>
                <option value="Electricidad">Electricidad</option>
                <option value="Aire Acondicionado">A/A</option>
                <option value="Pintura">Pintura</option>
                <option value="Administrativa">Administrativo</option>
                <option value="Sanitaria">Sanitaria</option>
            </select>

            <select wire:model.live="turno" class="p-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="">Turno</option>
                <option value="Matutino">Matutino</option>
                <option value="Vespertino">Vespertino</option>
                <option value="Nocturno">Nocturno</option>
            </select>
        </div>

        <!-- Tabla de funcionarios -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-center border-collapse" wire:poll.2s="filtrarFuncionarios">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="px-2 py-1">N° Cobro</th>
                        <th class="px-2 py-1">Nombre</th>
                        <th class="px-2 py-1">Apellido</th>
                        <th class="px-2 py-1">Área</th>
                        <th class="px-2 py-1">Fecha Inicio</th>
                        <th class="px-2 py-1">Fecha Fin</th>
                        <th class="px-2 py-1">Estado</th>
                        <th class="px-2 py-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funcionarios as $f)
                        <tr class="border-b hover:bg-gray-200 dark:hover:bg-gray-700">
                            <td class="px-2 py-1 cursor-pointer" 
                                wire:click="seleccionarFuncionario({{ $f->id }})">
                                {{ $f->numero_funcionario }}
                            </td>
                            <td class="px-2 py-1 cursor-pointer" 
                                wire:click="seleccionarFuncionario({{ $f->id }})">
                                {{ $f->nombre }}
                            </td>
                            <td class="px-2 py-1 cursor-pointer" 
                                wire:click="seleccionarFuncionario({{ $f->id }})">
                                {{ $f->apellido }}
                            </td>
                            <td class="px-2 py-1">{{ $f->area }}</td>
                            <td class="px-2 py-1">
                                {{ $f->fecha_inicio ? \Carbon\Carbon::parse($f->fecha_inicio)->format('d/m/y') : '-' }}
                            </td>
                            <td class="px-2 py-1">
                                {{ $f->fecha_fin ? \Carbon\Carbon::parse($f->fecha_fin)->format('d/m/y') : '-' }}
                            </td>
                            <td class="px-2 py-1">
                                @if($f->ultima_licencia_id)
                                    <select wire:change="cambiarEstado({{ $f->ultima_licencia_id }}, $event.target.value)"
                                        class="p-1 border rounded text-sm dark:bg-gray-700 dark:text-white">
                                        <option value="pendiente" {{ $f->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="autorizado" {{ $f->estado == 'autorizado' ? 'selected' : '' }}>Autorizado</option>
                                        <option value="no autorizado" {{ $f->estado == 'no autorizado' ? 'selected' : '' }}>No Autorizado</option>
                                    </select>
                                @endif
                            </td>
                            <td class="px-2 py-1 flex justify-center gap-2">
                                @if($f->ultima_licencia_id)
                                    <a href="{{ route('licencias.editar', $f->ultima_licencia_id) }}"
                                       class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Editar
                                    </a>
                                    <button type="button" 
                                            wire:click="$dispatch('mostrarAlerta', {{ $f->ultima_licencia_id }})"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                        Eliminar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-3 text-gray-500 dark:text-gray-400">
                                No se encontraron funcionarios con esos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

  <!-- Calendario del funcionario seleccionado -->
@if($numero_funcionario_seleccionado)
    <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                Calendario de Licencias de {{ $nombre_seleccionado }} {{ $apellido_seleccionado }}
            </h3>
            @if(!empty($licenciasFuncionarioSeleccionado))
                @foreach($licenciasFuncionarioSeleccionado as $licencia)
                    Días: {{ $licencia['cantidad_dias'] }}, Restantes: {{ $licencia['dias_restantes'] }}
                    @if(!$loop->last); @endif
                @endforeach
            @endif
            <button wire:click="volverALaLista"
                class="px-4 py-2 bg-red-500 text-white rounded shadow hover:bg-red-600">
                ← Volver a la lista
            </button>
        </div>

        @livewire('calendario-licencias', [
            'numero_funcionario' => $numero_funcionario_seleccionado,
            'nombre_seleccionado' => $nombre_seleccionado,
            'apellido_seleccionado' => $apellido_seleccionado
        ], key('funcionario-'.$numero_funcionario_seleccionado))
    </div>
@endif

<!-- Calendario filtrado por empresa / área / turno -->
@if($empresa || $area || $turno)
    <div class="bg-white dark:bg-gray-800 rounded shadow p-4 mt-6">
        <h3 class="font-semibold mb-3 text-center text-gray-800 dark:text-gray-200">
            Calendario de Licencias
        </h3>
        
        @livewire('calendario-licencias', [
    'empresa' => $empresa,
    'area' => $area,
    'turno' => $turno
], key('filtros-'.$empresa.'-'.$area.'-'.$turno)) </div>
@endif

</div>

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Livewire.on('mostrarAlerta', data => {
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
            Livewire.dispatch('confirmEliminarLicencia', data);
            Swal.fire('Eliminada', 'La Licencia se eliminó correctamente', 'success');
        }
    })
});


</script>
@endpush
