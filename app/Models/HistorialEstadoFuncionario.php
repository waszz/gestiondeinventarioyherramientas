<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorialEstadoFuncionario extends Model
{
    use HasFactory;

    protected $table = 'historial_estado_funcionarios';

    protected $fillable = [
        'funcionario_id',
        'estado',
        'inicio',
        'fin'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
    ];

    // Relación con Funcionario
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}