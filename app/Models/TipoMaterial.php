<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMaterial extends Model
{
    protected $table = 'tipo_materials'; // solo si tu tabla se llama así

    protected $fillable = [
        'nombre',
    ];

    public function materiales()
    {
        return $this->hasMany(Material::class);
    }
}