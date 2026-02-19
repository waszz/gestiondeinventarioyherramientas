<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'SO',
        'descripcion',
        'estado',
        'fechaSO',
        'user_id', // <- ahora apunta al usuario
        'lista_materiales',
    ];

    protected $casts = [
        'fechaSO' => 'date',
    ];

    // Relación con el usuario que creó la compra
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
