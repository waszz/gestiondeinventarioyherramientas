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
    'tipo_alimentacion'
];

protected static function booted()
{
    static::creating(function ($pedido) {

        // Si es pedido de herramienta, usar gci como número de seguimiento
        if ($pedido->tipo === 'herramienta' && isset($pedido->herramientas_id)) {
            $herramienta = Herramienta::find($pedido->herramientas_id);
            if ($herramienta) {
                $pedido->numero_seguimiento = $herramienta->gci_codigo;
                return; // salimos para no generar el número aleatorio
            }
        }

        // Para todo lo demás (materiales, etc.) mantenemos la lógica actual
        if (!$pedido->numero_seguimiento) { 
            $ultimo = self::orderBy('id', 'desc')->first();

            if ($ultimo && $ultimo->numero_seguimiento) {
                $numero = (int) str_replace('T-', '', $ultimo->numero_seguimiento);
                $numero++;
            } else {
                $numero = 1000000000;
            }

            $pedido->numero_seguimiento = 'T-' . $numero;
        }
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
