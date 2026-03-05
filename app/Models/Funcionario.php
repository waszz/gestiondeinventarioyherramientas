<?php

namespace App\Models;

use App\Models\HistorialEstadoFuncionario;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
   protected $fillable = [
    'numero_funcionario',
    'nombre',
    'apellido',
    'telefono',
    'cargo',
    'empresa',
    'area',
    'turno',
    'imagen',
    'estado',
];

public function historialEstados()
{
    return $this->hasMany(HistorialEstadoFuncionario::class);
}

public function cambiarEstadoConHistorial($estadoNuevo)
{
    if ($this->estado === $estadoNuevo) {
        return;
    }

    // Cerrar historial anterior
   $ultimo = $this->historialEstados()
    ->whereNull('fin')
    ->orderByDesc('inicio')
    ->first();

    if ($ultimo) {
        $ultimo->update([
            'fin' => now()
        ]);
    }

    // Crear nuevo historial
    $this->historialEstados()->create([
        'estado' => $estadoNuevo,
        'inicio' => now(),
        'fin' => null
    ]);

    $this->update([
        'estado' => $estadoNuevo
    ]);
}
}
