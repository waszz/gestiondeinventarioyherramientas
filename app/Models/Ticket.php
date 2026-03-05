<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
 protected $fillable = [
    'descripcion',
    'solicitado_por',
    'tipo_rotura',
    'status',
    'causa',
    'prioridad',
    'asignado_a',
    'categoria',
    'proyecto',
    'detalles',
    'sector_id',
    'lugar_id',
    'user_id',
    'funcionario_id',
];

public function sector()
{
    // Añadimos 'sector_id' para asegurar que busque por esa columna
    return $this->belongsTo(Sector::class, 'sector_id');
}

public function lugar()
{
    return $this->belongsTo(Lugar::class);
}
public function funcionario()
{
    return $this->belongsTo(Funcionario::class);
}

}
