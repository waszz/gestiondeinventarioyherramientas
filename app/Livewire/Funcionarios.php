<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Funcionario;

class Funcionarios extends Component
{
    use WithPagination;

    public $numero_funcionario, $nombre, $apellido, $cargo, $empresa, $area, $turno, $telefono;
    public $search = ''; // búsqueda única
    public $reloadFuncionario = 0;

    protected $listeners = ['eliminarFuncionario'];
    protected $paginationTheme = 'tailwind'; // usa tailwind para estilos

    public function mount()
    {
        // Seguridad: solo admin o supervisor pueden entrar
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }
    }

    public function updatingSearch()
    {
        // Resetea la página cuando se cambia el buscador
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset([
            'numero_funcionario', 'nombre', 'apellido', 'cargo', 'empresa', 'area', 'turno', 'telefono'
        ]);
    }

    public function guardar()
    {
        $this->validate([
            'numero_funcionario' => 'required|unique:funcionarios,numero_funcionario',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'empresa' => 'required|string|max:255',
            'area' => 'required|string',
            'turno' => 'required|string',
            'telefono' => 'required|string|max:20',
        ]);

        Funcionario::create([
            'numero_funcionario' => $this->numero_funcionario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cargo' => $this->cargo,
            'empresa' => $this->empresa,
            'area' => $this->area,
            'turno' => $this->turno,
            'telefono' => $this->telefono,
        ]);

        session()->flash('message', 'Funcionario creado.');

        $this->resetForm();
        $this->resetPage();
        $this->dispatch('funcionarioCreado');
    }

    public function eliminarFuncionario($id)
    {
        Funcionario::destroy($id);
        session()->flash('message', 'Funcionario eliminado.');
        $this->resetPage();
        $this->reloadFuncionario++;
    }

    public function render()
    {
        $query = Funcionario::query();

        if (!empty($this->search)) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->where('numero_funcionario', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('cargo', 'like', "%{$search}%")
                  ->orWhere('empresa', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('turno', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $funcionarios = $query->orderBy('numero_funcionario', 'asc')->paginate(10);


        return view('livewire.funcionarios', [
            'funcionarios' => $funcionarios
        ])->layout('layouts.app');
    }
}
