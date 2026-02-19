<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Compra;

class EditarCompra extends Component
{
    public $compra;       // La compra a editar
    public $SO;
    public $descripcion;
    public $estado;
    public $fechaSO;
    public $lista_materiales; // <-- nueva propiedad

    protected function rules()
    {
        return [
            'SO' => 'required|unique:compras,SO,' . $this->compra->id,
            'descripcion' => 'required|string|max:255',
            'estado' => 'required|in:compras,proveedor,en stock',
            'fechaSO' => 'required|date',
            'lista_materiales' => 'nullable|string|max:1000', // <-- validación para la lista
        ];
    }

    public function mount(Compra $compra)
    {
        // Seguridad: solo admin o supervisor
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }

        $this->compra = $compra;

        // Inicializar los campos con los valores actuales
        $this->SO = $compra->SO;
        $this->descripcion = $compra->descripcion;
        $this->estado = $compra->estado;
        $this->fechaSO = $compra->fechaSO->format('Y-m-d');
        $this->lista_materiales = $compra->lista_materiales; // <-- inicializa
    }

    public function actualizarCompra()
    {
        $this->validate();

        $this->compra->update([
            'SO' => $this->SO,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fechaSO' => $this->fechaSO,
            'lista_materiales' => $this->lista_materiales, // <-- actualizar lista
        ]);

        session()->flash('message', 'Compra actualizada correctamente.');

        return redirect()->route('compras.index'); // volver al listado
    }

    public function render()
    {
        return view('livewire.editar-compra')->layout('layouts.app');
    }
}
