<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
    'tipo',
    'nombre',
    'codigo',
    'sku',
    'cantidad',
    'estado',
    'observacion',
    'numero_seguimiento',
    'herramientas_id',
    'materiales_id',
    'stock_minimo',
];

    protected static function booted()
{
    static::creating(function ($pedido) {

        // Buscar último pedido creado
        $ultimo = self::orderBy('id', 'desc')->first();

        if ($ultimo && $ultimo->numero_seguimiento) {

            // Extraer número
            $numero = (int) str_replace('T-', '', $ultimo->numero_seguimiento);
            $numero++;

        } else {
            // Primer pedido del sistema
            $numero = 1000000000;
        }

        $pedido->numero_seguimiento = 'T-' . $numero;
    });
}

    // relaciones opcionales
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function herramienta()
    {
        return $this->belongsTo(Herramienta::class);
    }
}
