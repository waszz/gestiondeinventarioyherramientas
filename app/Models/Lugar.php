<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lugar extends Model
{
    use HasFactory;
protected $table = 'lugares';
    protected $fillable = [
        'nombre',
    ];

    /**
     * Un lugar pertenece a un sector
     */
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
}