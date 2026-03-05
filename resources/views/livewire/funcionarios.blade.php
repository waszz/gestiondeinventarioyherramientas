<div class="max-w-7xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
    <h2 class="text-2xl md:text-3xl font-bold mb-6 text-gray-800 dark:text-gray-200">Gestión de Funcionarios</h2>

    <!-- Mensaje flash -->
      @if(session()->has('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-xl shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- Buscador -->
    <div class="mb-6">
        <input type="text" wire:model.live="search"
               placeholder="Buscar funcionario (N°, nombre, apellido, cargo, empresa, área, turno, teléfono)..."
               class="w-full p-3 border rounded-lg shadow-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
    </div>


{{-- Importación --}}
<div class="flex bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-4">
    <input type="file" wire:model="archivoImportacion"
        class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-full">
</div>

@if($mostrarConfiguracion)
    <div class="bg-gray-50 p-4 rounded-2xl shadow-sm border border-gray-100 mb-4 grid grid-cols-2 gap-4">
        @foreach([
            'columnaNumeroFuncionario' => 'Número Funcionario',
            'columnaNombre' => 'Nombre',
            'columnaApellido' => 'Apellido',
            'columnaCargo' => 'Cargo',
            'columnaEmpresa' => 'Empresa',
            'columnaArea' => 'Área',
            'columnaTurno' => 'Turno',
            'columnaTelefono' => 'Teléfono',
        ] as $prop => $label)
            <div>
                <label class="text-gray-700 text-sm">{{ $label }}</label>
                <select wire:model="{{ $prop }}" class="w-full border rounded p-1 text-sm">
                    <option value="">-- Seleccionar columna --</option>
                    @foreach($columnasDetectadas as $columna)
                        <option value="{{ $columna }}">{{ $columna }}</option>
                    @endforeach
                </select>
                @error($prop) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        @endforeach

        @error('duplicado') <span class="text-red-500 col-span-2">{{ $message }}</span> @enderror

        {{-- BOTÓN IMPORTAR --}}
        <div class="col-span-2 flex justify-end mt-2">
            <button wire:click="importarFuncionarios"
                class="bg-gray-800 text-white px-4 py-2 rounded-xl hover:bg-black transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Importar Funcionarios
            </button>
        </div>
    </div>
@endif

    <!-- Formulario -->
    <form wire:submit.prevent="guardar" id="formFuncionario" class="space-y-6 mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Funcionario</label>
                <input type="text" wire:model="numero_funcionario"
                       class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                @error('numero_funcionario') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                <input type="text" wire:model="nombre"
                       class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                @error('nombre') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                <input type="text" wire:model="apellido"
                       class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                @error('apellido') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                <input type="text" wire:model="telefono"
                       class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                @error('telefono') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cargo</label>
                <select wire:model="cargo" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione un cargo</option>
                    <option value="Peon">Peón</option>
                    <option value="Oficial">Oficial</option>
                    <option value="Especializado">Especializado</option>
                    <option value="Administrativo">Administrativo</option>
                    <option value="Jefe Operativo">Jefe Operativo</option>
                    <option value="Jefe">Jefe</option>
                    <option value="Subjefe">Subjefe</option>
                </select>
                @error('cargo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Empresa</label>
                <select wire:model="empresa" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione una empresa</option>
                    <option value="Gente">Gente</option>
                    <option value="Prosepri">Prosepri</option>
                    <option value="Asoc.Española">Asoc.Española</option>
                </select>
                @error('empresa') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Área</label>
                <select wire:model="area" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione un área</option>
                    <option value="Reparaciones Generales">Rep. Grales</option>
                    <option value="Electricidad">Electricidad</option>
                    <option value="Aire Acondicionado">A/A</option>
                    <option value="Pintura">Pintura</option>
                    <option value="Administrativa">Administrativo</option>
                    <option value="Sanitaria">Sanitaria</option>
                </select>
                @error('area') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Turno</label>
                <select wire:model="turno" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione un turno</option>
                    <option value="Matutino">Matutino</option>
                    <option value="Vespertino">Vespertino</option>
                    <option value="Nocturno">Nocturno</option>
                </select>
                @error('turno') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">
        Imagen del funcionario
    </label>

    <input type="file" wire:model="imagen"
           class="mt-1 block w-full border rounded-lg p-2">

    @error('imagen') 
        <span class="text-red-500 text-sm">{{ $message }}</span> 
    @enderror

    {{-- Preview --}}
    @if ($imagen)
        <img src="{{ $imagen->temporaryUrl() }}"
             class="w-24 h-24 mt-2 rounded-full object-cover">
    @endif
</div>
        </div>

        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow-md">
            Crear Funcionario
        </button>
    </form>

    <!-- Tabla responsive -->
    <div class="overflow-x-auto rounded-lg shadow-md">
   @if(count($seleccionados) > 0)
    <div class="mb-4">
        <button wire:click="eliminarSeleccionados"
                class="bg-red-50 text-red-700 hover:bg-red-600 hover:text-white 
                       transition-all px-4 py-2 rounded-lg shadow">
            Eliminar seleccionados ({{ count($seleccionados) }})
        </button>
    </div>
@endif
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 uppercase text-xs font-semibold tracking-wider">
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
    <th class="p-4">N° Cobro</th>
    <th class="p-4">Nombre</th>
    <th class="p-4">Apellido</th>
    <th class="p-4">Tel.</th>
    <th class="p-4">Cargo</th>
    <th class="p-4">Empresa</th>
    <th class="p-4">Área</th>
    <th class="p-4">Turno</th>
    <th class="p-4">Acciones</th>
</tr>
</thead>
            <tbody class="text-gray-900 dark:text-gray-200 text-sm">
                @forelse($funcionarios as $f)
                    <tr class="text-center border-b dark:border-gray-600 hover:bg-blue-50/30 transition-colors">
                        <td class="p-3 text-center">
    <label class="relative flex justify-center items-center cursor-pointer group">
        
        <input type="checkbox"
               value="{{ $f->id }}"
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
                      d="M5 13l4 4L19 7" />
            </svg>

        </div>
    </label>
</td>
                        <td class="border px-3 py-2">{{ $f->numero_funcionario }}</td>
                        <td class="border px-3 py-2">{{ $f->nombre }}</td>
                        <td class="border px-3 py-2">{{ $f->apellido }}</td>
                        <td class="border px-3 py-2">{{ $f->telefono }}</td>
                        <td class="border px-3 py-2">{{ $f->cargo }}</td>
                        <td class="border px-3 py-2">{{ $f->empresa }}</td>
                        <td class="border px-3 py-2">{{ $f->area }}</td>
                        <td class="border px-3 py-2">{{ $f->turno }}</td>
                       <td class="border px-3 py-2 flex justify-center gap-2">
    <!-- Editar -->
    <a href="{{ route('funcionario.edit', $f->id) }}"
         class="p-2 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 114 4L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
        
    </a>

    <!-- Eliminar -->
    {{-- <button wire:click="$dispatch('confirmarEliminacion', { id: {{ $f->id }} })"
           class="p-2 bg-red-50 text-red-700 rounded-xl hover:bg-red-600 hover:text-white transition-all flex items-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
       
    </button> --}}
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-gray-500">No hay funcionarios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $funcionarios->links() }}
    </div>
</div>


@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    Livewire.on('confirmarEliminacion', data => {
    Swal.fire({
        title: '¿Eliminar Funcionario?',
        text: "Un funcionario eliminado no se puede recuperar",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarFuncionario', data);
            Swal.fire('Eliminado', 'El funcionario se eliminó correctamente', 'success');
        }
    })
});

Livewire.on('confirmarEliminacionMultiple', () => {
    Swal.fire({
        title: '¿Eliminar seleccionados?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarSeleccionados');
            Swal.fire('Eliminados', 'Funcionarios eliminados correctamente', 'success');
        }
    })
});
</script>
@endpush

@push('scripts')
    <script>
    Livewire.on('funcionarioCreado', () => {
        // Limpia los inputs manualmente si es necesario
        document.getElementById('formFuncionario').reset();
    });
</script>

<script>
    window.addEventListener('reload-page', event => {
        // Espera 5 segundos (5000 ms) antes de recargar
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
</script>

@endpush
