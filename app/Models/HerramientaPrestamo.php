<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HerramientaPrestamo extends Model
{
    protected $fillable = ['herramienta_id', 'funcionario_id', 'cantidad', 'estado', 'observaciones', 'cantidad_baterias'];

    public function herramienta()
    {
        return $this->belongsTo(Herramienta::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}
