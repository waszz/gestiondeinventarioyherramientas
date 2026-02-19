<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Funcionario;

class FuncionarioEdit extends Component
{
    public $funcionario_id;
    public $numero_funcionario, $nombre, $apellido, $cargo, $empresa;
    public $area, $turno, $telefono; // 🔹 agregado

    public function mount($id)
    {
        //Seguridad: solo admin o supervisor
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }

        $funcionario = Funcionario::findOrFail($id);

        $this->funcionario_id = $funcionario->id;
        $this->numero_funcionario = $funcionario->numero_funcionario;
        $this->nombre = $funcionario->nombre;
        $this->apellido = $funcionario->apellido;
        $this->cargo = $funcionario->cargo;
        $this->empresa = $funcionario->empresa;
        $this->area = $funcionario->area;   
        $this->turno = $funcionario->turno; 
        $this->telefono = $funcionario->telefono; // 🔹 agregado
    }

    public function actualizar()
    {
        $this->validate([
            'numero_funcionario' => 'required|unique:funcionarios,numero_funcionario,' . $this->funcionario_id,
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'empresa' => 'required|string|max:255',
            'area' => 'required|string|max:255',   
            'turno' => 'required|string|max:255',  
            'telefono' => 'nullable|string|max:20', // 🔹 validación para tel
        ]);

        Funcionario::find($this->funcionario_id)->update([
            'numero_funcionario' => $this->numero_funcionario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cargo' => $this->cargo,
            'empresa' => $this->empresa,
            'area' => $this->area,   
            'turno' => $this->turno, 
            'telefono' => $this->telefono, // 🔹 agregado
        ]);

        session()->flash('message', 'Funcionario actualizado.');
        return redirect()->route('funcionarios.index'); // Redirige a la lista
    }

    public function render()
    {
        return view('livewire.funcionario-edit')->layout('layouts.app');
    }
}
