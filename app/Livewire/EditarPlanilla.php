<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Planilla;
use Illuminate\Support\Facades\Gate; // si vas a usar Gates opcionales

class EditarPlanilla extends Component
{
    public $planillaId;
    public $horario_habitual;
    public $numero_funcionario;
    public $nombre;
    public $apellido;
    public $registro_faltas;
    public $fecha;
    public $horario_a_realizar;
    public $motivos;
    public $solicita;
    public $autoriza;
    public $fecha_inicio;
    public $fecha_fin;
    public $empresa;
    public $cargo;
    public $area;

    protected $rules = [
    'horario_habitual' => 'nullable|string|max:255',
    'numero_funcionario' => 'required|string|max:50',
    'nombre' => 'required|string|max:255',
    'apellido' => 'required|string|max:255',
    'registro_faltas' => 'nullable|string',
    'fecha_inicio' => 'nullable|date',
    'fecha_fin' => 'nullable|date',
    'horario_a_realizar' => 'nullable|string|max:255',
    'motivos' => 'nullable|string|max:255',
    'solicita' => 'required|string|max:255',
    'autoriza' => 'required|string|max:255',
    'empresa' => 'required|string|max:255',
    'cargo' => 'required|string|max:255',
    'area' => 'required|string|max:255',


];


  public function mount($id)
{
       //Seguridad: solo admin o supervisor
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }

    $planilla = Planilla::findOrFail($id);
    $this->planillaId = $planilla->id;
    $this->horario_habitual = $planilla->horario_habitual;
    $this->numero_funcionario = $planilla->numero_funcionario;
    $this->nombre = $planilla->nombre;
    $this->apellido = $planilla->apellido;
    $this->registro_faltas = $planilla->registro_faltas;
    $this->fecha_inicio = $planilla->fecha_inicio;
    $this->fecha_fin = $planilla->fecha_fin;
    $this->horario_a_realizar = $planilla->horario_a_realizar;
    $this->motivos = $planilla->motivos;
    $this->solicita = $planilla->solicita;
    $this->autoriza = $planilla->autoriza;
    $this->empresa = $planilla->empresa;
    $this->cargo = $planilla->cargo;
    $this->area = $planilla->area;

}
    public function actualizarPlanilla()
    {
        $this->validate();

        $planilla = Planilla::findOrFail($this->planillaId);
        $planilla->update([
            'horario_habitual' => $this->horario_habitual,
            'numero_funcionario' => $this->numero_funcionario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'registro_faltas' => $this->registro_faltas,
            'fecha' => $this->fecha,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'horario_a_realizar' => $this->horario_a_realizar,
            'motivos' => $this->motivos,
            'solicita' => $this->solicita,
            'autoriza' => $this->autoriza,
            'empresa' => $this->empresa,
            'cargo' => $this->cargo,
            'area' => $this->area,

        ]);

        session()->flash('mensaje', 'La solicitud se actualizó correctamente ✅');
        return redirect()->route('posts.index'); // o donde quieras redirigir
    }

    public function render()
    {
        return view('livewire.editar-planilla')->layout('layouts.app');
    }
}
