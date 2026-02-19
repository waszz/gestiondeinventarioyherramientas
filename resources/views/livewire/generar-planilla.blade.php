<div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Generar Planilla</h2>

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha desde</label>
            <input type="date" wire:model="fecha_desde" 
                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha hasta</label>
            <input type="date" wire:model="fecha_hasta" 
                   class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Empresa</label>
            <select wire:model="empresa" 
                    class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                <option value="">-- Seleccionar --</option>
                <option value="Gente">Gente</option>
                <option value="Prosepri">Prosepri</option>
                <option value="Asoc. Española">Asoc. Española</option>
            </select>
        </div>
    </div>

    <button wire:click="generar"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
        Generar
    </button>

  @if(count($planillas) > 0)
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full border border-gray-300 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 border">N° Cobro</th>
                    <th class="px-4 py-2 border">Nombre</th>
                    <th class="px-4 py-2 border">Apellido</th>
                    <th class="px-4 py-2 border">Cargo</th>
                    <th class="px-4 py-2 border">Area</th>
                    <th class="px-4 py-2 border">Horario habitual</th>
                    <th class="px-4 py-2 border">Solicita</th>
                    <th class="px-4 py-2 border">De la Fecha</th>
                    <th class="px-4 py-2 border">A la Fecha</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 dark:text-gray-200">
                @foreach($planillas as $p)
                    <tr class="text-center border-b border-gray-200 dark:border-gray-600">
                        <td class="border px-4 py-2">{{ $p->numero_funcionario }}</td>
                        <td class="border px-4 py-2">{{ $p->nombre }}</td>
                        <td class="border px-4 py-2">{{ $p->apellido }}</td>
                        <td class="border px-4 py-2">{{ $p->cargo }}</td>
                        <td class="border px-4 py-2">{{ $p->area }}</td>
                        <td class="border px-4 py-2">{{ $p->horario_habitual }}</td>
                        <td class="border px-4 py-2">{{ $p->solicita }}</td>
                        <td class="border px-4 py-2">{{ $p->fecha_inicio }}</td>
                        <td class="border px-4 py-2">{{ $p->fecha_fin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Botones de acción -->
        <div class="mt-4 flex gap-4">
           <a href="{{ route('planillas.imprimirFiltradas', ['empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]) }}" 
            target="_blank" 
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md uppercase">Imprimir
        </a>

            <a href="{{ route('planillas.pdfFiltradas', ['empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]) }}" 
                target="_blank" 
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md uppercase">Descargar PDF
            </a>
             
        </div>
    </div>
@endif
</div>
