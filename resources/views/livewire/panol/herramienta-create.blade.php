<div wire:poll.8s class="p-6 max-w-xl mx-auto">

    <h2 class="text-2xl font-bold mb-6">Crear Nueva Herramienta</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">

        {{-- Nombre --}}
        <div>
            <label class="block font-semibold">Nombre</label>
            <input type="text" wire:model="nombre" placeholder="Ej: Martillo"
                   class="w-full border rounded px-3 py-2">
            @error('nombre') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Código --}}
        <div>
            <label class="block font-semibold">Código</label>
            <input type="text" wire:model="codigo" placeholder="Ej: MTH-001"
                   class="w-full border rounded px-3 py-2">
            @error('codigo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Cantidad --}}
        <div>
            <label class="block font-semibold">Cantidad</label>
            <input type="number" wire:model="cantidad" placeholder="Ej: 10"
                   class="w-full border rounded px-3 py-2">
            @error('cantidad') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Botón --}}
        <button wire:click="guardar"
            class="bg-blue-600 text-white px-6 py-2 rounded font-semibold">
            Crear Herramienta
        </button>

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
