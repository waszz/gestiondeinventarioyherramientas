<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialHerramienta extends Model
{
    protected $table = 'historial_herramientas';

    protected $fillable = [
        'herramienta_id',
        'nombre',
        'codigo',
        'tipo',
        'cantidad',
        'funcionario',
        'detalle',
        'observacion',
    ];

    public function herramienta()
{
    return $this->belongsTo(Herramienta::class);
}

public function funcionario()
{
    return $this->belongsTo(Funcionario::class);
}

}
