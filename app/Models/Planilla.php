<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    protected $fillable = [
        'horario_habitual',
        'numero_funcionario',
        'nombre',
        'apellido',
        'registro_faltas',
        'horario_a_realizar',
        'motivos',
        'solicita',       
        'autoriza',       
        'user_id',            // quien creó
        'user_autoriza_id',   // quien autorizó
        'fecha_fin',
        'fecha_inicio',
        'empresa',
        'cargo',
        'area',
        'estado_autorizacion', // autorizado / no
    ];

    // Usuario que creó la planilla
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Usuario que autorizó la planilla
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'user_autoriza_id');
    }
}
