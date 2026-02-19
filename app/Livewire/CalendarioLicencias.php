<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Licencia;
use Carbon\Carbon;

class CalendarioLicencias extends Component
{
    // Las propiedades se mantienen igual
    public $empresa;
    public $area;
    public $turno;
    public $numero_funcionario = null;
    public $nombre_seleccionado = null;
    public $apellido_seleccionado = null;
    public $mesesConLicencias = [];
    public $licenciasPorMesYFuncionario = [];
    public $reloadLicencia = 0;
    public $estadosLicencias = [];

    // Ahora solo escuchamos un evento desde el padre para refrescar la vista.
    // La lógica de la base de datos se hará en el padre.
    protected $listeners = [
        'licenciaActualizada' => 'recargarDatos',
        'licenciaEliminada' => 'recargarDatos',
    ];

    public function mount(
        $empresa = null,
        $area = null,
        $turno = null,
        $numero_funcionario = null,
        $nombre_seleccionado = null,
        $apellido_seleccionado = null
    ) {
        $this->empresa = $empresa;
        $this->area = $area;
        $this->turno = $turno;
        $this->numero_funcionario = $numero_funcionario;
        $this->nombre_seleccionado = $nombre_seleccionado;
        $this->apellido_seleccionado = $apellido_seleccionado;
        $this->recargarDatos();
    }
    
    // Este método es llamado por el evento del padre para refrescar
    public function recargarDatos()
    {
        $this->cargarMeses();
    }

    // El método que se llama desde la vista para emitir un evento al padre
    public function emitirCambioEstado($licenciaId, $nuevoEstado)
    {
        // Emitimos el evento al componente padre ('licencias')
        // El padre ahora es el que se encarga de la lógica de la BD.
        $this->dispatch('cambiarEstado', $licenciaId, $nuevoEstado)->to('licencias');
    }

    // La lógica de cargar meses se mantiene, pero ahora se llama
    // desde mount y desde el evento de recarga.
    public function cargarMeses()
    {
        $query = Licencia::query()->with('funcionario');

        $query->when($this->numero_funcionario, function ($q) {
            $q->whereHas('funcionario', function ($q2) {
                $q2->where('nombre', $this->nombre_seleccionado)
                    ->where('apellido', $this->apellido_seleccionado);
            });
        });

        $query->when($this->empresa, fn($q) => $q->whereHas('funcionario', fn($q2) => $q2->where('empresa', $this->empresa)));
        $query->when($this->area, fn($q) => $q->whereHas('funcionario', fn($q2) => $q2->where('area', $this->area)));
        $query->when($this->turno, fn($q) => $q->whereHas('funcionario', fn($q2) => $q2->where('turno', $this->turno)));

        $licencias = $query->orderBy('fecha_inicio')->get();

        $meses = collect();
        foreach ($licencias as $licencia) {
            $inicio = Carbon::createFromFormat('Y-m-d', $licencia->fecha_inicio)->startOfMonth();
            $fin = Carbon::createFromFormat('Y-m-d', $licencia->fecha_fin)->startOfMonth();
            $mes = $inicio->copy();
            while ($mes <= $fin) {
                $meses->push($mes->format('Y-m'));
                $mes->addMonth();
            }
        }
        $this->mesesConLicencias = $meses->unique()->sort()->values()->toArray();

        $this->licenciasPorMesYFuncionario = [];
        foreach ($this->mesesConLicencias as $mesKey) {
            $licenciasMes = $licencias->filter(function ($l) use ($mesKey) {
                $inicio = Carbon::createFromFormat('Y-m-d', $l->fecha_inicio)->startOfMonth();
                $fin = Carbon::createFromFormat('Y-m-d', $l->fecha_fin)->startOfMonth();
                return $mesKey >= $inicio->format('Y-m') && $mesKey <= $fin->format('Y-m');
            });

            $this->licenciasPorMesYFuncionario[$mesKey] = $licenciasMes
                ->map(function ($l) {
                    $l->estado = $this->estadosLicencias[$l->id] ?? ($l->estado ?? 'pendiente');
                    return $l;
                })
                ->groupBy(fn($l) => $l->funcionario->nombre . ' ' . $l->funcionario->apellido)
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.calendario-licencias', [
            'mesesConLicencias' => $this->mesesConLicencias,
            'licenciasPorMesYFuncionario' => $this->licenciasPorMesYFuncionario,
            'estadosLicencias' => $this->estadosLicencias,
            'numero_funcionario' => $this->numero_funcionario,
            'nombre_seleccionado' => $this->nombre_seleccionado,
        ]);
    }
}
