<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Material;
use App\Models\Herramienta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class ReportesIndex extends Component
{
    public $tab = 'materiales'; // 'materiales' o 'herramientas'
    public $materiales;
    public $herramientas;
    public $emailDestino = '';
    public $mostrarModalEmail = false;

    // Filtros
    public $filtroEstado = ''; // ejemplo: 'activo', 'inactivo'
    public $filtroEsencial = ''; // solo para materiales: 'si' o 'no'

    public function mount()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $this->materiales = Material::orderBy('nombre')->get();
        $this->herramientas = Herramienta::orderBy('nombre')->get();
    }

    public function cambiarTab($tab)
    {
        $this->tab = $tab;
    }

    // Abrir PDF en el navegador (ver/imprimir)
public function imprimir($id)
{
    return redirect()->route('reportes.pdf', $id);
}

    // Descargar PDF
    public function descargarPDF()
    {
        $items = $this->aplicarFiltros();

        $pdf = Pdf::loadView('reportes.pdf', [
            'tipo'  => $this->tab,
            'items' => $items
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "reporte_{$this->tab}.pdf"
        );
    }

    // Abrir modal para enviar email
    public function abrirModalEmail()
    {
        $this->emailDestino = '';
        $this->mostrarModalEmail = true;
    }

    // Enviar PDF por email
    public function enviarEmail()
    {
        $this->validate([
            'emailDestino' => 'required|email'
        ]);

        $items = $this->aplicarFiltros();

        $pdf = Pdf::loadView('reportes.pdf', [
            'tipo'  => $this->tab,
            'items' => $items
        ])->setPaper('a4', 'landscape');

        Mail::send([], [], function($message) use ($pdf) {
            $message->to($this->emailDestino)
                    ->subject("Reporte {$this->tab}")
                    ->attachData($pdf->output(), "reporte_{$this->tab}.pdf")
                    ->setBody("Adjunto el reporte de {$this->tab} en PDF.");
        });

        $this->mostrarModalEmail = false;
        session()->flash('success', "Reporte enviado a {$this->emailDestino}");
    }

    // Aplica filtros a la colección de materiales o herramientas
    private function aplicarFiltros()
    {
        $items = $this->tab === 'materiales' ? $this->materiales : $this->herramientas;

        if ($this->filtroEstado) {
            $items = $items->filter(fn($i) => ($i->estado ?? 'activo') === $this->filtroEstado);
        }

        if ($this->tab === 'materiales' && $this->filtroEsencial) {
            $items = $items->filter(fn($i) => ($i->esencial ? 'si' : 'no') === strtolower($this->filtroEsencial));
        }

        return $items;
    }

    public function render()
    {
        return view('livewire.reportes-index')->layout('layouts.app');
    }
}
