<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Licencia;
use App\Models\Funcionario;

class EditarLicencia extends Component
{
    public $licencia_id;
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
    public $estado;

    protected $rules = [
        'numero_funcionario' => 'required|exists:funcionarios,numero_funcionario',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'empresa' => 'required|string',
        'area' => 'required|string',
        'turno' => 'required|string',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'presentismo' => 'boolean',
    ];

public function mount($id)
{
       // Seguridad: solo admin o supervisor
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }

    $licencia = Licencia::findOrFail($id);
    $funcionario = $licencia->funcionario;

    $this->licencia_id = $licencia->id;
    $this->numero_funcionario = $funcionario ? $funcionario->numero_funcionario : '';
    $this->nombre = $funcionario ? $funcionario->nombre : '';
    $this->apellido = $funcionario ? $funcionario->apellido : '';
    $this->empresa = $funcionario ? $funcionario->empresa : '';
    $this->area = $funcionario ? $funcionario->area : '';
    $this->turno = $funcionario ? $funcionario->turno : '';

    $this->fecha_inicio = $licencia->fecha_inicio;
    $this->fecha_fin = $licencia->fecha_fin;
    $this->cantidad_dias = $licencia->cantidad_dias;
    $this->presentismo = $licencia->presentismo;
    $this->dias_restantes = $licencia->dias_restantes;
    $this->estado = $licencia->estado; // <-- agregado
}

    // Autocompleta los datos del funcionario a partir del número
    public function generarDatos()
    {
        $funcionario = Funcionario::where('numero_funcionario', $this->numero_funcionario)->first();

        if (!$funcionario) {
            $this->reset(['nombre','apellido','empresa','area','turno']);
            session()->flash('error_funcionario', 'Funcionario no encontrado');
            return;
        }

        $this->nombre = $funcionario->nombre;
        $this->apellido = $funcionario->apellido;
        $this->empresa = $funcionario->empresa;
        $this->area = $funcionario->area;
        $this->turno = $funcionario->turno;
    }

    // Actualiza la licencia en la base de datos
    public function actualizarLicencia()
    {
        $this->validate();

        $licencia = Licencia::findOrFail($this->licencia_id);

        $licencia->update([
    'numero_funcionario' => $this->numero_funcionario,
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
    'estado' => $this->estado, // <-- agregado
]);


        session()->flash('message', 'Licencia actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.editar-licencia')->layout('layouts.app');
    }
}
