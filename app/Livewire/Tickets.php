<?php

namespace App\Livewire;

use App\Models\Funcionario;
use App\Models\Lugar;
use App\Models\Sector;
use App\Models\Ticket;
use Livewire\Component;

class Tickets extends Component
{
    
    public $descripcion;
    public $tipo_rotura;
    public $status = 'abierto';
    public $causa;
    public $prioridad = 'media';
    public $categoria;
    public $funcionario_id;
    public $proyecto;
    public $detalles;
    public $sector_id;
    public $lugar_id;
    public $nuevoSector;
    public $nuevoLugar;
    public $mostrarCrearSector = false;
    public $mostrarCrearLugar = false;
    public $confirmandoEliminarSector = false;
    public $confirmandoEliminarLugar = false;
    public $buscarFuncionario = '';
    public $filtroTipoRotura = '';
    public $filtroSector = '';
    public $filtroLugar = '';
    public $seleccionados = [];
    public $seleccionarTodos = false;
    public $filtroEstadoFuncionario = '';

    protected $rules = [
        'descripcion' => 'required|min:5',
        'funcionario_id' => 'required'
    ];


public function updatedSeleccionarTodos($value)
{
    if ($value) {
        $this->seleccionados = Ticket::pluck('id')->toArray();
    } else {
        $this->seleccionados = [];
    }
}

public function eliminarSeleccionados()
{
    if (count($this->seleccionados) == 0) return;

    Ticket::whereIn('id', $this->seleccionados)->delete();

    $this->seleccionados = [];
    $this->seleccionarTodos = false;

    session()->flash('success','Tickets eliminados correctamente');
    $this->dispatch('reload-page');
}

public function cambiarEstado($ticketId)
{
    $ticket = Ticket::find($ticketId);

    if(!$ticket) return;

    if($ticket->status == 'abierto'){
        $ticket->status = 'en_proceso';
    }
    elseif($ticket->status == 'en_proceso'){
        $ticket->status = 'cerrado';

        //  cuando se cierra el ticket
        if($ticket->funcionario_id){
            $funcionario = Funcionario::find($ticket->funcionario_id);

            if($funcionario){
                $funcionario->cambiarEstadoConHistorial('disponible');
            }
        }

    }
    else{
        $ticket->status = 'abierto';
    }

    $ticket->save();
      $this->dispatch('reload-page');
}

public function eliminarSector()
{
    if (!$this->sector_id) return;

    $tieneTickets = Ticket::where('sector_id', $this->sector_id)->exists();

    if ($tieneTickets) {
        session()->flash('error', 'No se puede eliminar un sector con tickets asociados.');
        $this->confirmandoEliminarSector = false;
        return;
    }

    Sector::find($this->sector_id)?->delete();

    $this->sector_id = null;
    $this->confirmandoEliminarSector = false;
}

public function eliminarLugar()
{
    if (!$this->lugar_id) return;

    $lugar = Lugar::find($this->lugar_id);

    if ($lugar) {
        $lugar->delete();
    }

    $this->lugar_id = null;
    $this->confirmandoEliminarLugar = false;
}

public function crearSector()
{
    $this->validate(['nuevoSector' => 'required|unique:sectores,nombre']);

    $sector = Sector::create(['nombre' => $this->nuevoSector]);

    $this->sector_id = $sector->id; // Aquí se asigna el nuevo ID (ej: el 4 o 5)
    $this->nuevoSector = null;
    $this->mostrarCrearSector = false;
}

public function crearLugar()
{
    $this->validate([
        'nuevoLugar' => 'required|unique:lugares,nombre'
    ]);

    $lugar = Lugar::create([
        'nombre' => $this->nuevoLugar
    ]);

    $this->lugar_id = $lugar->id;
    $this->nuevoLugar = null;
    $this->mostrarCrearLugar = false;
}

public function getSectoresProperty()
{
    return Sector::orderBy('nombre')->get();
}

public function getLugaresProperty()
{
    return Lugar::orderBy('nombre')->get();
}


 public function guardar()
{
    $this->validate();

    $funcionario = Funcionario::find($this->funcionario_id);

    if ($funcionario) {
        $funcionario->cambiarEstadoConHistorial('no_disponible');
    }

    Ticket::create([
        'descripcion' => $this->descripcion,
        'user_id' => auth()->id(),
        'funcionario_id' => $this->funcionario_id,
        'sector_id' => $this->sector_id,
        'lugar_id' => $this->lugar_id,
        'tipo_rotura' => $this->tipo_rotura,
        'status' => $this->status,
        'causa' => $this->causa,
        'prioridad' => $this->prioridad,
        'categoria' => $this->categoria,
        'proyecto' => $this->proyecto,
        'detalles' => $this->detalles,
    ]);

    $this->reset([
        'descripcion',
        'sector_id',
        'lugar_id',
        'tipo_rotura',
        'funcionario_id',
        'causa',
        'proyecto',
        'detalles',
    ]);

    $this->status = 'abierto';
    $this->prioridad = 'media';

    session()->flash('success', 'Ticket creado correctamente.');
    $this->dispatch('reload-page');
}

public function render()
{
    $query = Ticket::with(['funcionario','sector','lugar']);

    if ($this->buscarFuncionario) {
        $query->whereHas('funcionario', function($q){
            $q->where('nombre', 'like', '%' . $this->buscarFuncionario . '%');
        });
    }

    if ($this->filtroEstadoFuncionario) {
        $query->whereHas('funcionario', function($q){
            $q->where('estado', $this->filtroEstadoFuncionario);
        });
    }

    if ($this->filtroTipoRotura) {
        $query->where('tipo_rotura', $this->filtroTipoRotura);
    }

    if ($this->filtroSector) {
        $query->where('sector_id', $this->filtroSector);
    }

    if ($this->filtroLugar) {
        $query->where('lugar_id', $this->filtroLugar);
    }

    return view('livewire.tickets', [
        'funcionarios' => Funcionario::where('estado','disponible')
            ->orderBy('nombre')
            ->get(),
        'sectores' => Sector::orderBy('nombre')->get(),
        'lugares' => Lugar::orderBy('nombre')->get(),
        'tickets' => $query->latest()->get()
    ])->layout("layouts.app");
}
}