<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; 
use App\Models\Compra;

class VerCompras extends Component
{
    use WithPagination;

    public $reloadCompra = 0;
    public $search = '';
    public $expandedCompra = null;

    protected $listeners = [
        'eliminarCompra',
    ];

    // Para resetear a la página 1 al buscar
    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function queryCompras()
    {
        $query = Compra::query()->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('SO', 'like', '%' . $this->search . '%')
                ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                ->orWhere('estado', 'like', '%' . $this->search . '%')
                ->orWhere('lista_materiales', 'like', '%' . $this->search . '%');
            });
        }

        return $query;
    }

public function eliminarCompra($id)
{
   Compra::destroy($id);
        session()->flash('message', 'Compra eliminada.');
        $this->resetPage();
        $this->reloadCompra++;
}


    public function cambiarEstado($id, $nuevoEstado)
    {
        $compra = Compra::find($id);
        if ($compra && in_array($nuevoEstado, ['compras', 'proveedor', 'en stock'])) {
            $compra->estado = $nuevoEstado;
            $compra->save();

            session()->flash('message', 'Estado actualizado correctamente.');
           
        }
    }

    public function toggleExpanded($id)
    {
        $this->expandedCompra = $this->expandedCompra === $id ? null : $id;
        $this->reloadCompra++;
    }

    public function render()
    {
        // 🔹 Aquí aplicamos paginación (10 por página)
        $compras = $this->queryCompras()->paginate(10);

        return view('livewire.ver-compras', [
            'compras' => $compras,
        ])->layout('layouts.app');
    }
}
