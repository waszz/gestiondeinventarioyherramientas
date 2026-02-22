<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function materialesPdf()
    {
        $materiales = Material::orderBy('nombre')->get();

        $pdf = Pdf::loadView('pdf.materiales', compact('materiales'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('materiales.pdf');
    }
}