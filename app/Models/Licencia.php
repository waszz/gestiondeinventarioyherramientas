<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'empresa',
        'area',
        'turno',
        'fecha_inicio',
        'fecha_fin',
        'cantidad_dias',
        'presentismo',
        'dias_restantes',
        'estado',
        'funcionario_id',
    ];

    public function funcionario()
{
    return $this->belongsTo(Funcionario::class);
}
}
