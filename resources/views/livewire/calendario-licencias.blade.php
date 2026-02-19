<div class="space-y-6">
    @foreach($mesesConLicencias as $mesKey)
        @php
            $fecha = \Carbon\Carbon::createFromFormat('Y-m', $mesKey);
            $funcionariosMes = $licenciasPorMesYFuncionario[$mesKey] ?? [];
            $primerDiaMes = $fecha->copy()->startOfMonth();
            $ultimoDiaMes = $fecha->copy()->endOfMonth();
            $primerDiaSemana = $primerDiaMes->dayOfWeekIso;
            $diasDelMes = [];

            for ($i = 1; $i < $primerDiaSemana; $i++) {
                $diasDelMes[] = null;
            }

            for ($dia = 1; $dia <= $primerDiaMes->daysInMonth; $dia++) {
                $diasDelMes[] = $dia;
            }
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
            <h3 class="font-semibold mb-3 text-center text-gray-800 dark:text-gray-200">
                {{ $fecha->format('F Y') }}
            </h3>
            
            <div class="grid grid-cols-7 gap-1 text-center mb-2 text-gray-700 dark:text-gray-300">
                @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $diaSemana)
                    <div class="font-semibold">{{ $diaSemana }}</div>
                @endforeach
            </div>

            @foreach($funcionariosMes as $nombreFuncionario => $licenciasFuncionario)
                @if(!$numero_funcionario || $nombreFuncionario == ($nombre_seleccionado . ' ' . $apellido_seleccionado))
                    @php $licenciasFuncionario = $licenciasFuncionario ?? []; @endphp
                    <div class="mb-2">
                        <div class="text-left font-semibold text-gray-800 dark:text-gray-200 mb-1">
                            {{ $nombreFuncionario }}
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center">
                            @foreach($diasDelMes as $dia)
                                @if($dia)
                                    @php
                                        $fechaDiaCarbon = \Carbon\Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-') . str_pad($dia, 2, '0', STR_PAD_LEFT));
                                        $licenciaDia = collect($licenciasFuncionario)->first(function($l) use ($fechaDiaCarbon) {
                                            return isset($l['fecha_inicio'], $l['fecha_fin']) &&
                                                \Carbon\Carbon::createFromFormat('Y-m-d', $l['fecha_inicio'])->lte($fechaDiaCarbon) &&
                                                \Carbon\Carbon::createFromFormat('Y-m-d', $l['fecha_fin'])->gte($fechaDiaCarbon);
                                        });

                                        if($licenciaDia){
                                            $licenciaId = $licenciaDia['id'];
                                            $estado = $estadosLicencias[$licenciaId] ?? ($licenciaDia['estado'] ?? 'pendiente');
                                            switch($estado){
                                                case 'autorizado':
                                                    $colorBg = 'bg-green-300 dark:bg-green-600 text-gray-800 dark:text-gray-100';
                                                    break;
                                                case 'no autorizado':
                                                    $colorBg = 'bg-red-300 dark:bg-red-600 text-gray-800 dark:text-gray-100';
                                                    break;
                                                default:
                                                    $colorBg = 'bg-orange-300 dark:bg-orange-600 text-gray-800 dark:text-gray-100';
                                                    break;
                                            }
                                            $nuevoEstado = $estado === 'autorizado' ? 'no autorizado' : 'autorizado';
                                        } else {
                                            $colorBg = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
                                            $licenciaId = null;
                                        }
                                    @endphp

                                    <div class="p-2 border rounded {{ $colorBg }}"
                                        @if($licenciaId)
                                            {{-- Corregido: Ahora llama al método del componente hijo que emitirá el evento --}}
                                            wire:click="emitirCambioEstado({{ $licenciaId }}, '{{ $nuevoEstado }}')"
                                        @endif>
                                        <div>{{ $dia }}</div>
                                    </div>
                                @else
                                    <div></div> {{-- Celda vacía para alinear días --}}
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</div>
