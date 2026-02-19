<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoMaterial extends Model
{

use HasFactory;

    protected $table = 'movimientos_material';
    protected $fillable = [
        'material_id',
        'tipo',
        'cantidad',
        'motivo',
        'usuario',
        'destino',
        'ticket',
        'funcionario_id',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
