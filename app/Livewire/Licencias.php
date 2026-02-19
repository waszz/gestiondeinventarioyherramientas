<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Licencia;
use App\Models\Funcionario;

class Licencias extends Component
{
    use WithPagination;

    public $numero_funcionario;
    public $nombre;
    public $apellido;
    public $empresa;
    public $area;
    public $turno;

    public $numero_funcionario_seleccionado;
    public $nombre_seleccionado;
    public $apellido_seleccionado;

    public $funcionarios = [];
    public $licenciasFuncionario = [];
    public $reloadLicencia = 0;
    public $licenciasFuncionarioSeleccionado = [];

    // Array para guardar estados temporales de las licencias
    public $estadosLicencias = [];

    protected $paginationTheme = 'tailwind';

    protected $listeners = [
        'confirmEliminarLicencia' => 'handleEliminarLicencia',
        // Se corrigió el listener para escuchar el evento "cambiarEstado"
        'cambiarEstado' => 'cambiarEstado',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
       
    }

    public function seleccionarFuncionario($id)
    {
        $funcionario = Funcionario::find($id);

        if ($funcionario) {
            $this->numero_funcionario_seleccionado = $funcionario->numero_funcionario;
            $this->nombre_seleccionado = $funcionario->nombre;
            $this->apellido_seleccionado = $funcionario->apellido;
            $this->cargarLicencias($funcionario);

            $this->licenciasFuncionarioSeleccionado = $this->licenciasFuncionario->map(function($l){
                return [
                    'cantidad_dias' => $l->cantidad_dias,
                    'dias_restantes' => $l->dias_restantes,
                ];
            })->toArray();

            $this->filtrarFuncionarios();
            $this->reloadLicencia++;
        }
    }

    public function volverALaLista()
    {
        $this->numero_funcionario_seleccionado = null;
        $this->nombre_seleccionado = null;
        $this->apellido_seleccionado = null;
        $this->licenciasFuncionario = [];
        $this->filtrarFuncionarios();
    }

    public function cambiarEstado($licenciaId, $nuevoEstado)
    {
        $this->estadosLicencias[$licenciaId] = $nuevoEstado;

        $licencia = Licencia::find($licenciaId);
        if ($licencia) {
            $licencia->estado = $nuevoEstado;
            $licencia->save();
        }

        // El componente hijo "CalendarioLicencias" debe escuchar
        // este evento para recargar sus datos.
        $this->dispatch('licenciaActualizada');
        
        $this->filtrarFuncionarios();
    }

    public function updatedEmpresa() { $this->filtrarFuncionarios(); }
    public function updatedArea() { $this->filtrarFuncionarios(); }
    public function updatedTurno() { $this->filtrarFuncionarios(); }
    public function updatedNumeroFuncionario() { $this->filtrarFuncionarios(); }

  public function filtrarFuncionarios()
{
    $query = Funcionario::query();

    $query->when($this->empresa, fn($q) => $q->where('empresa', $this->empresa));
    $query->when($this->area, fn($q) => $q->where('area', $this->area));
    $query->when($this->turno, fn($q) => $q->where('turno', $this->turno));

    if ($this->numero_funcionario) {
        $query->where(function($q) {
            $q->where('numero_funcionario', 'like', '%' . $this->numero_funcionario . '%')
              ->orWhere('nombre', 'like', '%' . $this->numero_funcionario . '%')
              ->orWhere('apellido', 'like', '%' . $this->numero_funcionario . '%');
        });
    }

    $query->orderBy('numero_funcionario', 'asc');
    $funcionarios = $query->get();

    $this->funcionarios = $funcionarios->map(function($f){
        // Cargar la última licencia filtrando por empresa/área/turno
        $ultimaLicencia = Licencia::where('nombre', $f->nombre)
            ->where('apellido', $f->apellido)
            ->when($this->empresa, fn($q) => $q->where('empresa', $this->empresa))
            ->when($this->area, fn($q) => $q->where('area', $this->area))
            ->when($this->turno, fn($q) => $q->where('turno', $this->turno))
            ->orderBy('fecha_inicio', 'desc')
            ->first();

        $f->fecha_inicio = $ultimaLicencia?->fecha_inicio;
        $f->fecha_fin = $ultimaLicencia?->fecha_fin;
        $f->ultima_licencia_id = $ultimaLicencia?->id;
        $f->estado = $this->estadosLicencias[$f->ultima_licencia_id] 
                         ?? $ultimaLicencia?->estado 
                         ?? 'pendiente';

        return $f;
    });
}


   private function cargarLicencias($funcionario)
{
    $query = Licencia::where('nombre', $funcionario->nombre)
                     ->where('apellido', $funcionario->apellido);

    // Aplicar filtros si están seleccionados
    if ($this->empresa) {
        $query->where('empresa', $this->empresa);
    }
    if ($this->area) {
        $query->where('area', $this->area);
    }
    if ($this->turno) {
        $query->where('turno', $this->turno);
    }

    $this->licenciasFuncionario = $query->orderBy('fecha_inicio', 'desc')->get();

    foreach ($this->licenciasFuncionario as $licencia) {
        $this->estadosLicencias[$licencia->id] = $licencia->estado;
    }
}


    public function handleEliminarLicencia($licenciaId)
    {
        $licencia = Licencia::find($licenciaId);

        if ($licencia) {
            $licencia->delete();
            unset($this->estadosLicencias[$licenciaId]);
            $this->licenciasFuncionario = collect($this->licenciasFuncionario)
                ->filter(fn($l) => $l->id != $licenciaId)
                ->values();
            session()->flash('message', 'Licencia eliminada correctamente.');
            $this->dispatch('licenciaEliminada');
            $this->filtrarFuncionarios();
            $this->reloadLicencia++;
        }
    }

    public function render()
    {
      
        return view('livewire.licencias', [
            'funcionarios' => $this->funcionarios,
            'licenciasFuncionario' => $this->licenciasFuncionario,
        ])->layout('layouts.app');
    }
}
