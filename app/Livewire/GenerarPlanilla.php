<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Planilla;
use Illuminate\Support\Carbon;

class GenerarPlanilla extends Component
{
    public $fecha_desde;
    public $fecha_hasta;
    public $empresa;
    public $planillas = [];

    public function mount() {
          //  Seguridad: solo admin o supervisor pueden entrar
        if (!auth()->check()) {
            abort(403, 'Esta acción no está autorizada.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }
    }

    public function generar()
{
    $this->validate([
        'fecha_desde' => 'required|date',
        'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        'empresa' => 'required|string',
    ]);

    $this->planillas = Planilla::query()
    ->where('empresa', $this->empresa)
    ->where('estado_autorizacion', 'autorizado')
    ->where(function ($q) {
        $desde = Carbon::parse($this->fecha_desde)->startOfDay();
        $hasta = Carbon::parse($this->fecha_hasta)->endOfDay();

        // condición: que el rango elegido se cruce con el rango de la planilla
        $q->whereBetween('fecha_inicio', [$desde, $hasta])
          ->orWhereBetween('fecha_fin', [$desde, $hasta])
          ->orWhere(function ($q2) use ($desde, $hasta) {
              $q2->where('fecha_inicio', '<=', $desde)
                 ->where('fecha_fin', '>=', $hasta);
          });
    })
    ->get();

}

    public function render()
    {
        return view('livewire.generar-planilla')->layout('layouts.app');
    }
}
