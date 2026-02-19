<div wire:poll.2s="checkCambios">
<!-- Campo de búsqueda y botón -->
<div class="mb-6 flex flex-col sm:flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex flex-col sm:flex-col md:flex-row gap-2 w-full md:w-2/3">
        <!-- Buscar por texto (funcionario) -->
        <input type="text" 
               wire:model="search" 
               class="p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                      text-gray-900 dark:text-gray-200 rounded-md w-full"
               placeholder="Buscar por nombre, apellido o número de funcionario.."
               @if(!($isAdmin || $isSupervisor)) disabled @endif>

        <!-- Buscar por fecha exacta -->
        <input type="date"
               wire:model="searchDate"
               class="p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 
                      text-gray-900 dark:text-gray-200 rounded-md w-full"
               @if(!($isAdmin || $isSupervisor)) disabled @endif>

        <!-- Buscar por mes -->
        <input type="month" wire:model="searchMonth" 
               class="p-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900
                      dark:text-gray-200 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500
                      dark:focus:ring-blue-400 text-lg font-medium cursor-pointer transition-all duration-200
                      hover:border-blue-400 dark:hover:border-blue-500" 
               @if(!($isAdmin || $isSupervisor)) disabled @endif>

        <!-- Buscar por usuario creador -->
        <input type="text"
               wire:model="searchUser"
               class="p-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-200 rounded-md w-full"
               placeholder="Buscar por usuario.."
               @if(!($isAdmin || $isSupervisor)) disabled @endif>
    </div>

    <div class="mt-2 md:mt-0">
        @if(!($isAdmin || $isSupervisor))
            <p class="text-red-500 text-sm">Solo administradores o supervisores pueden buscar.</p>
        @else
            <button wire:click="buscarPlanillas" 
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 
                           text-white dark:text-gray-200 py-2 px-6 rounded-lg text-sm font-semibold transition w-full md:w-auto">
                Buscar
            </button>
        @endif
    </div>
</div>

  

    <!-- Mensaje flash -->
    @if(session()->has('mensaje'))
        <div class="p-3 text-center text-green-600 dark:text-green-400 font-semibold">
            {{ session('mensaje') }}
        </div>
    @endif

 <!-- Planillas -->
@forelse ($planillas as $planilla)
    <div 
        @class([
            'p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6 hover:shadow-lg transition border',
            'border-green-500' => $planilla->estado_autorizacion === 'autorizado',
            'border-red-500' => $planilla->estado_autorizacion === 'no',
            'border-gray-200 dark:border-gray-600' => is_null($planilla->estado_autorizacion)
        ])
    >
        <div class="text-center font-bold mb-4">
            <span @class([
                'px-3 py-1 rounded',
                'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200' => $planilla->estado_autorizacion === 'autorizado',
                'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200' => $planilla->estado_autorizacion === 'no',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200' => is_null($planilla->estado_autorizacion)
            ])>
                {{ $planilla->estado_autorizacion === 'autorizado' ? 'Autorizado' : ($planilla->estado_autorizacion === 'no' ? 'No autorizado' : 'Sin estado') }}
            </span>
        </div>

        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6">
            <!-- Datos de la planilla -->
            <div class="flex-1 space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Creado por:</strong> {{ $planilla->user->name ?? 'Sin usuario' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Creada el:</strong> {{ \Carbon\Carbon::parse($planilla->getRawOriginal('created_at'))->locale('es')->isoFormat('D [de] MMMM [de] YYYY HH:mm') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Horario Habitual:</strong> {{ $planilla->horario_habitual }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Número de Funcionario:</strong> {{ $planilla->numero_funcionario }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Nombre de Funcionario:</strong> {{ $planilla->nombre }} {{ $planilla->apellido }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Empresa:</strong> {{ $planilla->empresa }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Cargo:</strong> {{ $planilla->cargo }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Área:</strong> {{ $planilla->area }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Registro de Faltas:</strong> {{ $planilla->registro_faltas }}</p>
                <p class="text-sm text-blue-600 dark:text-blue-400"><strong>Solicita:</strong> {{ $planilla->solicita }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>De la Fecha:</strong> {{ \Carbon\Carbon::parse($planilla->fecha_inicio)->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>A la Fecha:</strong> {{ \Carbon\Carbon::parse($planilla->fecha_fin)->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Horario a realizar:</strong> {{ $planilla->horario_a_realizar }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Motivos:</strong> {{ $planilla->motivos }}</p>
                <p class="text-sm text-green-600 dark:text-green-400"><strong>Firma:</strong> {{ $planilla->autoriza }}</p>
            </div>

            <!-- Acciones -->
            <div class="flex flex-col gap-4 md:w-48 w-full mt-4 md:mt-0">
                <!-- Editar y Eliminar (propietario o supervisor) -->
                @if(auth()->id() === $planilla->user_id || $isSupervisor)
                    <a href="{{ route('planillas.editar', $planilla->id) }}" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white dark:text-gray-200 py-2 px-4 rounded-lg text-xs font-bold uppercase text-center transition">Editar</a>

                    <div class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white dark:text-gray-200 py-2 px-4 rounded-lg text-xs font-bold uppercase text-center" wire:key="planilla-{{ $reloadPlanilla }}">
                        <button wire:click="$dispatch('mostrarAlerta', { id: {{ $planilla->id }} })" class="uppercase">Eliminar</button>
                    </div>
                @endif

                <!-- Autorizado / No autorizado (solo supervisor) -->
                @if($isSupervisor)
                    <button wire:click="cambiarEstado({{ $planilla->id }}, 'autorizado')" class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white dark:text-gray-200 py-2 px-4 rounded-lg text-xs font-bold uppercase transition">Autorizado</button>
                    <button wire:click="cambiarEstado({{ $planilla->id }}, 'no')" class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white dark:text-gray-200 py-2 px-4 rounded-lg text-xs font-bold uppercase transition">No autorizado</button>
                @endif

                <!-- PDF / Imprimir (todos) -->
                <a href="{{ route('planillas.pdf', $planilla->id) }}" 
                   target="_blank"
                   class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-xs font-bold uppercase text-center mt-2">
                   DESCARGAR PDF
                </a>

                <button onclick="imprimirPlanilla({{ $planilla->id }})" 
                        class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg text-xs font-bold uppercase mt-2">
                    Imprimir
                </button>
            </div>
        </div>
    </div>
@empty
    <p class="p-3 text-center text-sm text-gray-600 dark:text-gray-300">No hay planillas registradas.</p>
@endforelse


    <!-- Paginación -->
    <div class="mt-10 mb-2">
        {{ $planillas->links() }}
    </div>
</div>


@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
Livewire.on('mostrarAlerta', planillaId => {
    Swal.fire({
        title: '¿Eliminar Planilla?',
        text: "Una planilla eliminada no se puede recuperar",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarPlanilla', planillaId);
            Swal.fire('Eliminada', 'La planilla se eliminó correctamente', 'success');
        }
    })
});

</script>
@endpush

@push('scripts')
<script>
function imprimirPlanilla(id) {
    const url = "{{ url('planillas') }}/" + id + "/imprimir";
    const ventana = window.open(url, '_blank');
    ventana.focus();
    ventana.onload = function() {
        ventana.print();
    };
}
</script>
@endpush