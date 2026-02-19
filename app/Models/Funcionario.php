<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $fillable = [
        'numero_funcionario',
        'nombre',
        'apellido',
        'cargo',
        'empresa',
        'turno',
        'area',
        'telefono',
       
    ];
}
