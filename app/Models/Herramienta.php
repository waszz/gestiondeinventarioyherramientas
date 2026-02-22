<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Herramienta extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'gci_codigo',
        'cantidad', 
        'estado',
        'cantidad_disponible',
        'cantidad_prestamo',
        'cantidad_fuera_servicio',
        'tipo_alimentacion',
        'baterias_stock',

    ];

    /**
     * Actualizar las cantidades según el estado
     */
    public function actualizarCantidades()
    {
        // Total de herramientas
        $total = $this->cantidad_disponible + $this->cantidad_prestamo + $this->cantidad_fuera_servicio;

        // Ajusta cantidad total si es diferente
        $this->cantidad = $total;
        $this->save();
    }

    // Accesores para mostrar en Blade
    public function getCantidadDisponibleAttribute($value)
    {
        return $value ?? 0;
    }

    public function getCantidadPrestamoAttribute($value)
    {
        return $value ?? 0;
    }

    public function getCantidadFueraServicioAttribute($value)
    {
        return $value ?? 0;
    }

    public function prestamos()
    {
        return $this->hasMany(HerramientaPrestamo::class);
    }
    public function fueraServicios()
    {
        return $this->hasMany(FueraServicio::class);
    }
        public function pedidos()
{
    return $this->hasMany(Pedido::class, 'herramientas_id');
}

}
