<?php

namespace App\Http\Controllers;

use App\Models\MovimientoMaterial;
use Barryvdh\DomPDF\Facade\Pdf;

class HistorialController extends Controller
{
    public function exportar()
    {
        $movimientos = MovimientoMaterial::with('material','funcionario')
            ->orderBy('created_at','desc')
            ->get();

        if ($movimientos->isEmpty()) {
            abort(404, 'No hay movimientos para exportar.');
        }

        $pdf = Pdf::loadView('pdf.historial-materiales', compact('movimientos'))
            ->setPaper('A4', 'landscape');

        return $pdf->download(
            'historial_materiales_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}