<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bateria extends Model
{
    protected $fillable = ['stock_total'];

    public static function stock()
    {
        return self::first()?->stock_total ?? 0;
    }

    public static function actualizarStock($cantidad)
{
    $registro = self::firstOrCreate(
        [],
        ['stock_total' => 0]
    );

    $registro->stock_total = $cantidad;
    $registro->save();
}
}