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
        'material_esencial',
        'gci_codigo',
        'tipo_material_id',
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoMaterial::class);
    }
    public function pedidos()
{
    return $this->hasMany(Pedido::class, 'materiales_id');
}

    public function tipo()
    {
        return $this->belongsTo(TipoMaterial::class, 'tipo_material_id');
    }

}
