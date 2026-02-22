<?php

namespace App\Livewire\Panol;

use Livewire\Component;
use App\Models\Herramienta;

class HerramientaCreate extends Component
{
    public $nombre;
    public $codigo;
    public $cantidad;
    public $tipo_alimentacion;
    public $gci_codigo;


    protected $rules = [
        'nombre' => 'required|string|max:255',
        'codigo' => 'required|string|max:50|unique:herramientas,codigo',
        'gci_codigo' => 'nullable|string|max:50|unique:herramientas,gci_codigo',
        'cantidad' => 'required|integer|min:1',
        'tipo_alimentacion' => 'nullable|in:bateria,cable', 
    ];

    public function guardar()
    {
        $this->validate();

     Herramienta::create([
    'nombre' => $this->nombre,
    'codigo' => $this->codigo,
    'gci_codigo' => $this->gci_codigo,
    'cantidad' => $this->cantidad,
    'tipo_alimentacion' => $this->tipo_alimentacion, 
    'cantidad_disponible' => $this->cantidad,
    'cantidad_prestamo' => 0,
    'cantidad_fuera_servicio' => 0,
    'estado' => 'disponible',
]);

        // Limpiar los campos
        $this->nombre = '';
        $this->codigo = '';
        $this->gci_codigo = '';
        $this->cantidad = null;
        $this->tipo_alimentacion = null; 

        $this->dispatch('reload-page');

        session()->flash('success', 'Herramienta creada correctamente.');
    }

    public function render()
    {
        return view('livewire.panol.herramienta-create')->layout('layouts.app');
    }
}