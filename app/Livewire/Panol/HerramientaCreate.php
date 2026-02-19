<?php

namespace App\Livewire\Panol;

use Livewire\Component;
use App\Models\Herramienta;

class HerramientaCreate extends Component
{
    public $nombre;
    public $codigo;
    public $cantidad;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'codigo' => 'required|string|max:50|unique:herramientas,codigo',
        'cantidad' => 'required|integer|min:1',
    ];

    public function guardar()
    {
        $this->validate();

        Herramienta::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'cantidad' => $this->cantidad,                // Total
            'cantidad_disponible' => $this->cantidad,    // Todo disponible al inicio
            'cantidad_prestamo' => 0,                    // Inicialmente nada prestado
            'cantidad_fuera_servicio' => 0,             // Inicialmente nada fuera de servicio
            'estado' => 'disponible',                   // Estado inicial
        ]);

        // Limpiar los campos
        $this->nombre = '';
        $this->codigo = '';
        $this->cantidad = null;
        $this->dispatch('reload-page');

        session()->flash('success', 'Herramienta creada correctamente.');
    }

    public function render()
    {
        return view('livewire.panol.herramienta-create')->layout('layouts.app');
    }
}

