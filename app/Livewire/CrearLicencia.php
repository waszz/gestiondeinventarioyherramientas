<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Licencia;
use App\Models\Funcionario;

class CrearLicencia extends Component
{
    public $numero_funcionario;
    public $nombre;
    public $apellido;
    public $empresa;
    public $area;
    public $turno;
    public $fecha_inicio;
    public $fecha_fin;
    public $cantidad_dias;      // solo referencia
    public $presentismo = true;
    public $dias_restantes;     // solo referencia

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'empresa' => 'required|string',
        'area' => 'required|string',
        'turno' => 'required|string',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'presentismo' => 'boolean',
    ];

    public function mount() {
           // Seguridad: solo admin o supervisor
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }
    }
    public function generarDatos()
    {
        $funcionario = Funcionario::where('numero_funcionario', $this->numero_funcionario)->first();

        if (!$funcionario) {
            $this->reset(['nombre','apellido','empresa','area','turno']);
            session()->flash('error_funcionario', 'Funcionario no encontrado');
            return;
        }

        // Llenar automáticamente
        $this->nombre = $funcionario->nombre;
        $this->apellido = $funcionario->apellido;
        $this->empresa = $funcionario->empresa;
        $this->area = $funcionario->area;
        $this->turno = $funcionario->turno;
    }

   public function guardarLicencia()
{
    $this->validate();

    $funcionario = Funcionario::where('numero_funcionario', $this->numero_funcionario)->first();

    Licencia::create([
        'funcionario_id' => $funcionario ? $funcionario->id : null,
        'nombre' => $this->nombre,
        'apellido' => $this->apellido,
        'empresa' => $this->empresa,
        'area' => $this->area,
        'turno' => $this->turno,
        'fecha_inicio' => $this->fecha_inicio,
        'fecha_fin' => $this->fecha_fin,
        'cantidad_dias' => $this->cantidad_dias,
        'presentismo' => $this->presentismo,
        'dias_restantes' => $this->dias_restantes,
    ]);

    session()->flash('message', 'Licencia creada correctamente.');

    $this->reset([
        'numero_funcionario','nombre','apellido','empresa','area','turno',
        'fecha_inicio','fecha_fin','cantidad_dias','presentismo','dias_restantes'
    ]);

    $this->dispatch('licenciaCreada');
   
}

   

    public function render()
    {
        return view('livewire.crear-licencia')->layout('layouts.app');
    }
}
