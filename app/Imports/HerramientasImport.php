<?php

namespace App\Imports;

use App\Models\Herramienta;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;

class HerramientasImport implements ToModel
{
    protected $columnaNombre;
    protected $columnaCodigo;
    protected $columnaGci;
    protected $columnaAlimentacion;
    protected $columnaCantidad;

    public function __construct(
        $columnaNombre,
        $columnaCodigo,
        $columnaGci = null,
        $columnaAlimentacion = null,
        $columnaCantidad
    ) {
        $this->columnaNombre       = $columnaNombre;
        $this->columnaCodigo       = $columnaCodigo;
        $this->columnaGci          = $columnaGci;
        $this->columnaAlimentacion = $columnaAlimentacion;
        $this->columnaCantidad     = $columnaCantidad;
    }

  public function model(array $row)
{
    $nombre = trim($row[$this->columnaNombre] ?? '');
    $codigo = trim($row[$this->columnaCodigo] ?? '');
    $gci    = trim($row[$this->columnaGci] ?? '');
    $alimentacionRaw = strtolower(trim($row[$this->columnaAlimentacion] ?? ''));
    $cantidad = (int) ($row[$this->columnaCantidad] ?? 0);

    // Ignorar filas vacías
    if ($nombre === '' || $codigo === '') {
        return null;
    }

    //Normalizar tipo alimentación
    $alimentacion = 'no_aplica'; // valor por defecto (manual)

    if ($alimentacionRaw !== '') {

        // Quitar tildes por seguridad
        $alimentacionRaw = str_replace(
            ['á','é','í','ó','ú'],
            ['a','e','i','o','u'],
            $alimentacionRaw
        );

        if (str_contains($alimentacionRaw, 'bat')) {
            $alimentacion = 'bateria';

        } elseif (
            str_contains($alimentacionRaw, 'cab') ||
            str_contains($alimentacionRaw, '220') ||
            str_contains($alimentacionRaw, 'elect')
        ) {
            $alimentacion = 'cable';

        } elseif (
            str_contains($alimentacionRaw, 'manual') ||
            str_contains($alimentacionRaw, 'no aplica') ||
            str_contains($alimentacionRaw, 'ninguna')
        ) {
            $alimentacion = 'no_aplica';
        }
    }

    return new Herramienta([
        'nombre' => $nombre,
        'codigo' => $codigo,
        'gci' => $gci ?: null,
        'tipo_alimentacion' => $alimentacion,

        // Stock inicial correcto
        'cantidad' => $cantidad,
        'cantidad_disponible' => $cantidad,
        'cantidad_prestamo' => 0,
        'cantidad_fuera_servicio' => 0,
    ]);
}
}