<?php

namespace App\Livewire;

use App\Models\Funcionario;
use App\Models\HerramientaPrestamo;
use App\Models\HistorialEstadoFuncionario;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeguimientoFuncionarios extends Component
{
    use WithFileUploads;

    public $nombre;
    public $apellido;
    public $cargo;
    public $empresa;
    public $area;
    public $turno;
    public $imagen;
    public $estado = 'disponible';
    public $imagenNueva;
    public $funcionarioEditandoId = null;
    public $buscar = '';
    public $filtroArea = '';
    public $historialAbierto = null;
    public $historialAbiertos = [];
    public $funcionarioSeleccionado = null;

    protected $rules = [
        'nombre' => 'required',
        'apellido' => 'required',
        'cargo' => 'required',
        'empresa' => 'required',
        'area' => 'required',
        'turno' => 'required',
        'imagen' => 'nullable|image|max:2048',
        'estado' => 'required'
    ];

public function exportarPDF($funcionarioId)
{
    $funcionario = Funcionario::with('historialEstados')->findOrFail($funcionarioId);

    $historial = $funcionario->historialEstados->sortByDesc('inicio');

    $pdf = Pdf::loadView('pdf.historial-funcionario', compact('funcionario', 'historial'));

    return response()->streamDownload(
        fn () => print($pdf->output()),
        'historial_'.$funcionario->nombre.'.pdf'
    );
}

public function exportarCSV($funcionarioId)
{
    $funcionario = Funcionario::with('historialEstados')->findOrFail($funcionarioId);

    $fileName = 'historial_'.$funcionario->nombre.'.csv';

    return response()->streamDownload(function () use ($funcionario) {

        $handle = fopen('php://output', 'w');

        fputcsv($handle, [
            'Estado',
            'Inicio',
            'Fin',
            'Duración'
        ]);

        foreach ($funcionario->historialEstados->sortByDesc('inicio') as $h) {

            fputcsv($handle, [
                $h->estado,
                $h->inicio,
                $h->fin ?? 'En curso',
                $h->fin
                    ? $h->inicio->diffForHumans($h->fin, true)
                    : 'Activo'
            ]);
        }

        fclose($handle);

    }, $fileName);
}

public function toggleHistorial($id)
{
    if (in_array($id, $this->historialAbiertos)) {
        $this->historialAbiertos = array_diff($this->historialAbiertos, [$id]);
    } else {
        $this->historialAbiertos[] = $id;
    }
}

    public function actualizarImagen($id)
{
    $this->validate([
        'imagenNueva' => 'required|image|max:2048'
    ]);

    $funcionario = Funcionario::findOrFail($id);

    // Borrar imagen anterior si existe
    if ($funcionario->imagen && Storage::disk('public')->exists($funcionario->imagen)) {
        Storage::disk('public')->delete($funcionario->imagen);
    }

    // Guardar nueva imagen
    $ruta = $this->imagenNueva->store('funcionarios', 'public');

    $funcionario->update([
        'imagen' => $ruta
    ]);

    $this->reset('imagenNueva', 'funcionarioEditandoId');

    session()->flash('success', 'Imagen actualizada correctamente.');
}


   

public function cambiarEstado($id, $estadoNuevo)
{
    $funcionario = Funcionario::findOrFail($id);

    // Si intentan ponerlo disponible manualmente
    if ($estadoNuevo == 'disponible') {

        $ticketsAbiertos = Ticket::where('funcionario_id', $funcionario->id)
            ->where('status', '!=', 'cerrado')
            ->exists();

        $tienePrestamos = HerramientaPrestamo::where('funcionario_id', $funcionario->id)
            ->where('estado', 'prestada')
            ->exists();

        if ($ticketsAbiertos || $tienePrestamos) {
            session()->flash('error', 'No se puede poner disponible porque tiene tickets abiertos o herramientas prestadas.');
            return;
        }
    }

    $funcionario->cambiarEstadoConHistorial($estadoNuevo);
}

   public function guardar()
{
    $this->validate();

    $rutaImagen = null;

    if ($this->imagen) {
        $rutaImagen = $this->imagen->store('funcionarios', 'public');
    }

    //Crear funcionario
    $funcionario = Funcionario::create([
        'nombre' => $this->nombre,
        'apellido' => $this->apellido,
        'cargo' => $this->cargo,
        'empresa' => $this->empresa,
        'area' => $this->area,
        'turno' => $this->turno,
        'imagen' => $rutaImagen,
        'estado' => $this->estado,
    ]);

    // Crear primer registro en historial
    HistorialEstadoFuncionario::create([
        'funcionario_id' => $funcionario->id,
        'estado' => $this->estado,
        'inicio' => now(),
        'fin' => null
    ]);

    // Resetear formulario
    $this->reset();
    $this->estado = 'disponible';

    session()->flash('success', 'Funcionario creado correctamente.');
}

 public function render()
{
    $query = Funcionario::query();

    if ($this->buscar) {
        $query->where(function ($q) {
            $q->where('nombre', 'like', '%' . $this->buscar . '%')
              ->orWhere('apellido', 'like', '%' . $this->buscar . '%');
        });
    }

    if ($this->filtroArea) {
        $query->where('area', $this->filtroArea);
    }

    return view('livewire.seguimiento-funcionarios', [
        'funcionarios' => $query->latest()->get(),
        'funcionarioHistorial' => $this->funcionarioSeleccionado
            ? Funcionario::with('historialEstados')->find($this->funcionarioSeleccionado)
            : null,
        'areas' => Funcionario::select('area')->distinct()->pluck('area')
    ])->layout("layouts.app");
}
}