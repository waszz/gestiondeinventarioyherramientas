<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Herramienta;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function mostrarPDF($tipo)
    {
        $items = $tipo === 'materiales'
            ? Material::orderBy('nombre')->get()
            : Herramienta::orderBy('nombre')->get();

        $pdf = Pdf::loadView('reportes.pdf', [
            'tipo' => $tipo,
            'items' => $items,
        ])->setPaper('a4', 'landscape');

        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

        return $pdf->stream("reporte_{$tipo}.pdf");
    }
}
