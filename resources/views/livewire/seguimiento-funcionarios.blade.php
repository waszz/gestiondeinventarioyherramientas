<div>

    <div class="flex gap-4 mb-4">

        {{-- Buscar --}}
        <input type="text"
               wire:model.live="buscar"
               placeholder="Buscar funcionario..."
               class="border rounded-lg p-2 w-1/3">

        {{-- Filtro área --}}
        <select wire:model.live="filtroArea"
                class="border rounded-lg p-2 w-1/4">
            <option value="">Todas las áreas</option>
            @foreach($areas as $area)
                <option value="{{ $area }}">{{ $area }}</option>
            @endforeach
        </select>

    </div>

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 items-start mt-6 max-h-[600px] overflow-y-auto pr-2" >

        @foreach($funcionarios as $f)

            <div class="bg-white rounded-xl shadow p-3 text-center border-4 transition-all duration-300
                @if($f->estado == 'disponible')
                    border-green-500
                @elseif($f->estado == 'no_disponible')
                    border-gray-400
                @elseif($f->estado == 'falta')
                    border-red-500
                @endif
            ">

                {{-- Imagen --}}
                <div class="relative">

                    @if($f->imagen)
                        <img src="{{ asset('storage/'.$f->imagen) }}"
                             class="w-16 h-16 mx-auto rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 mx-auto rounded-full bg-gray-200"></div>
                    @endif

                    <button wire:click="$set('funcionarioEditandoId', {{ $f->id }})"
                            class="text-xs text-blue-500 mt-1 hover:underline">
                        Cambiar
                    </button>

                    @if($funcionarioEditandoId === $f->id)

                        <input type="file" wire:model="imagenNueva"
                               class="mt-2 text-xs">

                        @if($imagenNueva)
                            <img src="{{ $imagenNueva->temporaryUrl() }}"
                                 class="w-16 h-16 mx-auto rounded-full object-cover mt-2">
                        @endif

                        <button wire:click="actualizarImagen({{ $f->id }})"
                                class="mt-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                            Guardar
                        </button>

                    @endif

                </div>

                {{-- Nombre --}}
                <h3 class="font-semibold text-sm mt-2 truncate">
                    {{ $f->nombre }} {{ $f->apellido }}
                </h3>

                {{-- Cargo --}}
                <p class="text-xs text-gray-500 truncate">
                    {{ $f->cargo }} - {{ $f->area }}
                </p>

                {{-- Estado --}}
                <select wire:change="cambiarEstado({{ $f->id }}, $event.target.value)"
                        class="mt-2 border rounded-lg p-1 text-xs w-full">

                    <option value="disponible" {{ $f->estado == 'disponible' ? 'selected' : '' }}>
                        Disponible
                    </option>

                    <option value="no_disponible" {{ $f->estado == 'no_disponible' ? 'selected' : '' }}>
                        No disponible
                    </option>

                    <option value="falta" {{ $f->estado == 'falta' ? 'selected' : '' }}>
                        Falta
                    </option>

                </select>

     {{-- SECCIÓN ACCIONES HISTORIAL --}}
<div class="mt-3 space-y-2">

    {{-- Botón principal --}}
    <button wire:click="toggleHistorial({{ $f->id }})"
        class="w-full flex items-center justify-center gap-2 
               bg-indigo-50 hover:bg-indigo-100 
               text-indigo-700 font-semibold text-xs 
               py-2 rounded-xl transition-all duration-300 
               border border-indigo-200">

        {{-- Icono historial --}}
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-4 h-4"
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z" />
        </svg>

        {{ in_array($f->id, $historialAbiertos) ? 'Ocultar historial' : 'Ver historial' }}

    </button>

    {{-- Descargas --}}
    <div class="flex gap-2">

        {{-- PDF --}}
        <button wire:click="exportarPDF({{ $f->id }})"
            class="flex-1 flex items-center justify-center gap-1
                   bg-red-50 hover:bg-red-100 
                   text-red-600 font-medium text-xs
                   py-2 rounded-xl transition-all duration-300
                   border border-red-200">

            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="w-4 h-4" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12v-8m0 8l-3-3m3 3l3-3" />
            </svg>

            PDF
        </button>

        {{-- CSV --}}
        <button wire:click="exportarCSV({{ $f->id }})"
            class="flex-1 flex items-center justify-center gap-1
                   bg-emerald-50 hover:bg-emerald-100 
                   text-emerald-600 font-medium text-xs
                   py-2 rounded-xl transition-all duration-300
                   border border-emerald-200">

            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="w-4 h-4" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12v-8m0 8l-3-3m3 3l3-3" />
            </svg>

            CSV
        </button>

    </div>

</div>

                {{-- PANEL HISTORIAL (DENTRO DE LA CARD) --}}
                @if(in_array($f->id, $historialAbiertos))

                    <div class="mt-3 text-left text-xs bg-gray-50 p-3 rounded-lg max-h-56 overflow-y-auto">

                        @forelse($f->historialEstados->sortByDesc('inicio') as $h)

                            <div class="border-b py-2">

                                <div class="text-sm font-bold text-gray-800">
                                    {{ $f->nombre }} {{ $f->apellido }}
                                </div>

                                <div class="font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $h->estado)) }}
                                </div>

                                <div>
                                    Inicio: {{ $h->inicio->format('d/m/Y H:i') }}
                                </div>

                                <div>
                                    Fin:
                                    @if($h->fin)
                                        {{ $h->fin->format('d/m/Y H:i') }}
                                    @else
                                        En curso
                                    @endif
                                </div>

                                <div class="text-gray-500">
                                    Duración:
                                    @if($h->fin)
                                        {{ $h->inicio->diffForHumans($h->fin, true) }}
                                    @else
                                        Activo
                                    @endif
                                </div>

                            </div>

                        @empty
                            <div>No hay historial</div>
                        @endforelse

                    </div>

                @endif

            </div>

        @endforeach

    </div>

</div>