<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\MovimientoMaterial;
use App\Models\TipoMaterial;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class MaterialesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $nombre = trim($row['descripcion'] ?? '');
        $gci = trim($row['articulo'] ?? '');

        if ($nombre === '') {
            return null;
        }

        // --- Detectar tipo automáticamente ---
        $tipo = $this->detectarTipo($nombre);

        $material = Material::create([
            'nombre'            => $nombre,
            'codigo_referencia' => 'IMP-' . strtoupper(Str::random(8)),
            'gci_codigo'        => $gci ?: null,
            'stock_actual'      => 0,
            'stock_minimo'      => 0,
            'material_esencial' => false,
            'tipo_material_id'  => $tipo?->id,
        ]);

        // Crear movimiento inicial de stock en cero
        MovimientoMaterial::create([
            'material_id' => $material->id,
            'tipo'        => 'entrada',
            'cantidad'    => 0,
            'motivo'      => 'Importación inicial desde Excel',
        ]);

        return $material;
    }

    private function detectarTipo(string $nombreMaterial)
    {
        $nombre = strtolower($nombreMaterial);
        $nombre = str_replace(
            ['á','é','í','ó','ú','ü','ñ'],
            ['a','e','i','o','u','u','n'],
            $nombre
        );

        $tipos = [
            'Eléctrico' => [
                'led', 'lampara', 'interruptor', 'cable', 'tomacorriente', 'driver', 'modulo', 'pulsador', 
                'proyector', 'fuente', 'luminaria', 'shucko', 'unipolar', 'trifasico', 'termico', 'zocalo', 
                'baliza', 'alargue', 'ficha', 'condensador', 'transductor', 'relee', 'bobina', 'conexion', 
                'caja estanco', 'caja exterior', 'cable usb', 'cable hdmi', 'detector de gas'
            ],
            'Sanitario' => [
                'awaduct', 'termofusion', 'cobre', 'bronce', 'valvula', 'caño', 'codo', 'tee', 'cupla', 
                'niple', 'entrerrosca', 'cisterna', 'inodoro', 'bacha', 'sifon', 'griferia', 'monocomando', 
                'ducha', 'flexible', 'adhesivo pvc', 'detentor', 'radiador', 'bomba centrifuga', 'purga', 
                'manguito', 'ramal', 'pileta patio', 'junta elastomerica'
            ],
            'Construccion' => [
                'perfil', 'amstrong', 'yeso', 'mdf', 'placa', 'cemento', 'pintura', 'incalex', 'incalux', 
                'esmalte', 'silicona', 'masilla', 'pastina', 'adhesivo', 'arena', 'ticholo', 'ceramica', 
                'porcelanato', 'azulejo', 'zocalo poliuretanico', 'lana de vidrio', 'membrana', 'fijador', 
                'revoque', 'hormigonera', 'mortero', 'canto abs', 'tapajunta'
            ],
            'Herramientas' => [
                'taladro', 'rotomartillo', 'amoladora', 'mecha', 'broca', 'destornillador', 'pinza', 
                'martillo', 'trincheta', 'espatula', 'nivel', 'metro', 'cinta metrica', 'disco de corte', 
                'disco flap', 'llave torx', 'punta phillips', 'esmeril', 'escalera', 'rasqueta', 'pincel', 'rodillo'
            ],
            'Herrería' => [
                'cerradura', 'star', 'kallay', 'cerrojo', 'pestillo', 'bisagra', 'hierro', 'angulo', 
                'chapa', 'varilla', 'electrodo', 'candado', 'portacandado', 'planchuela', 'metal desplegado'
            ],
            'Servicios' => [
                'trabajos de', 'reparacion de', 'mantenimiento', 'alquiler de', 'certificacion', 'desagote'
            ],
            'Otros' => [
                'guante', 'lente', 'bateria', 'precinto', 'pila', 'filtro bolsa', 'filtro descartable', 
                'equipo de lluvia', 'mascara 3m', 'malla sombra', 'soga', 'tanza', 'abrazadera'
            ]
        ];

        foreach ($tipos as $nombreTipo => $palabras) {
            foreach ($palabras as $palabra) {
                $p = strtolower($palabra);
                $p = str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $p);

                // Si el nombre contiene la palabra clave, asignamos el tipo
                if (str_contains($nombre, $p)) {
                    return TipoMaterial::firstOrCreate(['nombre' => $nombreTipo]);
                }
            }
        }

        return TipoMaterial::firstOrCreate(['nombre' => 'Otros']);
    }
}