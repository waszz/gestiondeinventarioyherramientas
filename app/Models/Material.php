<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiales';

    protected $fillable = [
        'nombre', 
        'codigo_referencia',
        'stock_actual',
        'stock_minimo',
        'material_esencial'
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoMaterial::class);
    }
    public function pedidos()
{
    return $this->hasMany(Pedido::class, 'materiales_id');
}
}
