<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FueraServicio extends Model
{
    protected $table = 'fuera_servicio';
    protected $fillable = ['herramienta_id', 'cantidad', 'motivo'];

    public function herramienta()
    {
        return $this->belongsTo(Herramienta::class);
    }
}
