<div class="w-full flex justify-center mt-10">

    <div class="p-6 max-w-xl w-full">

        <h2 class="text-2xl font-bold mb-6 text-center">Añadir Nuevo Material</h2>

        @if (session()->has('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">

            {{-- Nombre --}}
            <div>
                <label class="block font-semibold">Nombre</label>
                <input type="text" wire:model="nombre"
                       placeholder="Ej: Tornillos"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- Código --}}
            <div>
                <label class="block font-semibold">Código de Referencia</label>
                <input type="text" wire:model="codigo_referencia"
                       placeholder="Ej: TRN-001"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- Cantidad inicial --}}
            <div>
                <label class="block font-semibold">Cantidad Inicial</label>
                <input type="number" wire:model="cantidad_inicial"
                       placeholder="Ej: 100"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- Stock mínimo --}}
            <div>
                <label class="block font-semibold">Stock Mínimo</label>
                <input type="number" wire:model="stock_minimo"
                       placeholder="Ej: 20"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- Material esencial --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model="material_esencial">
                <label>Material Esencial</label>
            </div>

            {{-- Botón --}}
            <button wire:click="guardar"
                class="bg-blue-600 text-white px-6 py-2 rounded font-semibold w-full">
                Añadir Material
            </button>

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
</div>
