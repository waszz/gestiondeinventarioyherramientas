<?php

namespace App\Livewire\Panol;

use Livewire\Component;
use App\Models\Material;
use App\Models\MovimientoMaterial;

class MaterialCreate extends Component
{
    public $nombre;
    public $codigo_referencia;
    public $gci_codigo;
    public $cantidad_inicial = 0;
    public $stock_minimo = 0;
    public $material_esencial = false;
    protected $listeners = ['refreshHerramientas' => '$refresh'];

   public function guardar()
{
    $this->validate([
        'nombre' => 'required',
        'codigo_referencia' => 'required|unique:materiales,codigo_referencia',
        'gci_codigo' => 'required|unique:materiales,gci_codigo',
        'cantidad_inicial' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
    ]);

    $material = Material::create([
        'nombre' => $this->nombre,
        'codigo_referencia' => $this->codigo_referencia,
        'gci_codigo' => $this->gci_codigo,
        'stock_actual' => $this->cantidad_inicial,
        'stock_minimo' => $this->stock_minimo,
        'material_esencial' => $this->material_esencial,
    ]);

    if ($this->cantidad_inicial > 0) {
        MovimientoMaterial::create([
            'material_id' => $material->id,
            'tipo' => 'entrada',
            'cantidad' => $this->cantidad_inicial,
            'motivo' => 'Stock inicial',
            'usuario' => auth()->user()->name ?? 'Sistema',
        ]);
    }

    $this->reset();
    $this->dispatch('reload-page');

    session()->flash('success', 'Material creado correctamente');
}

    public function render()
    {
        return view('livewire.panol.material-create')->layout('layouts.app');
    }
}
