<?php

namespace App\Imports;

use App\Models\Funcionario;
use Maatwebsite\Excel\Concerns\ToModel;

class FuncionariosImport implements ToModel
{
    protected array $columnas;

    public function __construct(array $columnas)
    {
        $this->columnas = $columnas;
    }

    public function model(array $row)
    {
        // Extraer columnas usando los nombres del array
        $numero   = trim($row[$this->columnas['numero']] ?? '');
        $nombre   = trim($row[$this->columnas['nombre']] ?? '');
        $apellido = trim($row[$this->columnas['apellido']] ?? '');
        $cargo    = trim($row[$this->columnas['cargo']] ?? '');
        $empresa  = trim($row[$this->columnas['empresa']] ?? '');
        $area     = trim($row[$this->columnas['area']] ?? '');
        $turno    = trim($row[$this->columnas['turno']] ?? '');
        $telefono = trim($row[$this->columnas['telefono']] ?? '');

        if ($numero === '' && $nombre === '') return null;

        // Evitar duplicados y actualizar si existe
        return Funcionario::updateOrCreate(
            ['numero_funcionario' => $numero],
            [
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'cargo'    => $cargo,
                'empresa'  => $empresa,
                'area'     => $area,
                'turno'    => $turno,
                'telefono' => $telefono,
            ]
        );
    }
}