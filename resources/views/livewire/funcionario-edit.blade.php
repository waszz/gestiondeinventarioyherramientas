<div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Editar Funcionario</h2>

    @if(session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="actualizar" class="space-y-4">
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

        <!-- 🔹 Teléfono -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
            <input type="text" wire:model="telefono"
                   class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white"
                   placeholder="Ej: 099123456">
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

        <!-- Select de Área -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Área</label>
            <select wire:model="area" class="w-full p-2 border rounded-md dark:bg-gray-700 dark:text-white">
                <option value="">Seleccione un área</option>
                <option value="Reparaciones Generales">Rep. Grales</option>
                <option value="Electricidad">Electricidad</option>
                <option value="Aire Acondicionado">A/A</option>
                <option value="Pintura">Pintura</option>
                <option value="Administrativa">Administrativo</option>
            </select>
            @error('area') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Select de Turno -->
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

        <div class="flex gap-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Actualizar
            </button>
            <a href="{{ route('funcionarios.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Cancelar
            </a>
        </div>
    </form>
</div>
