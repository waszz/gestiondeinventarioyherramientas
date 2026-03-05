<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sector extends Model
{
    use HasFactory;
protected $table = 'sectores';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Un sector tiene muchos lugares
     */
    public function lugares()
    {
        return $this->hasMany(Lugar::class);
    }
}