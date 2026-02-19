<div class="max-w-5xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Gestión de Funcionarios</h2>

    <!-- Mensaje flash -->
    @if(session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Buscar funcionario (N°, nombre, apellido, cargo, empresa, área, turno, teléfono)..."
            class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
    </div>

    <!-- Formulario -->
    <form wire:submit.prevent="guardar" id="formFuncionario" class="space-y-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Inputs del formulario -->
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

            <!-- 🔹 Nuevo input Teléfono -->
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

            <!-- Nuevo select Área -->
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

            <!-- 🔹 Nuevo select Turno -->
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
        </div>

        <button type="submit"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
            Crear
        </button>
    </form>

    <!-- Tabla de funcionarios -->
    <table class="min-w-full border border-gray-300 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <tr>
                <th class="px-3 py-2 border">N° Cobro</th>
                <th class="px-3 py-2 border">Nombre</th>
                <th class="px-3 py-2 border">Apellido</th>
                <th class="px-3 py-2 border">Tel.</th>
                <th class="px-3 py-2 border">Cargo</th>
                <th class="px-3 py-2 border">Empresa</th>
                <th class="px-3 py-2 border">Área</th>
                <th class="px-3 py-2 border">Turno</th>
                <th class="px-3 py-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-900 dark:text-gray-200 text-sm">
            @forelse($funcionarios as $f)
                <tr class="text-center border-b dark:border-gray-600">
                    <td class="border px-3 py-2">{{ $f->numero_funcionario }}</td>
                    <td class="border px-3 py-2">{{ $f->nombre }}</td>
                    <td class="border px-3 py-2">{{ $f->apellido }}</td>
                    <td class="border px-3 py-2">{{ $f->telefono }}</td> 
                    <td class="border px-3 py-2">{{ $f->cargo }}</td>
                    <td class="border px-3 py-2">{{ $f->empresa }}</td>
                    <td class="border px-3 py-2">{{ $f->area }}</td>
                    <td class="border px-3 py-2">{{ $f->turno }}</td>
                   <td class=" border px-3 py-2 flex justify-center gap-3">
    <a href="{{ route('funcionario.edit', $f->id) }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm">
        Editar
    </a>
    <button wire:click="$dispatch('confirmarEliminacion', { id: {{ $f->id }} })"
        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 text-center rounded-md text-sm">
        Eliminar
    </button>
</td>

                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-gray-500">No hay funcionarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 🔹 Links de paginación -->
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
</script>
@endpush

@push('scripts')
    <script>
    Livewire.on('funcionarioCreado', () => {
        // Limpia los inputs manualmente si es necesario
        document.getElementById('formFuncionario').reset();
    });
</script>
@endpush
