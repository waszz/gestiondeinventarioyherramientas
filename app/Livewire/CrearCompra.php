<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Compra;

class CrearCompra extends Component
{
    public $SO;
    public $descripcion;
    public $estado = 'compras'; // valor por defecto
    public $fechaSO;
    public $lista_materiales; // nuevo campo

    protected $rules = [
        'SO' => 'required|unique:compras,SO',
        'descripcion' => 'required|string|max:255',
        'estado' => 'required|in:compras,proveedor,en stock',
        'fechaSO' => 'required|date',
        'lista_materiales' => 'nullable|string|max:500', // opcional
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

    public function guardarCompra()
    {
        $this->validate();

        Compra::create([
            'SO' => $this->SO,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fechaSO' => $this->fechaSO,
            'lista_materiales' => $this->lista_materiales, // guardamos el nuevo campo
            'user_id' => auth()->id(), // <- asigna el usuario logueado
        ]);

        // Limpiar campos después de guardar
        $this->reset(['SO', 'descripcion', 'fechaSO', 'lista_materiales']);
        $this->estado = 'compras';

        session()->flash('message', 'Compra creada correctamente.');
        return redirect()->route('compras.index');
    }

    public function render()
    {
        return view('livewire.crear-compra')->layout('layouts.app');
    }
}
