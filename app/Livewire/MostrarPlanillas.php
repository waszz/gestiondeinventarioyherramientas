<?php
namespace App\Livewire;

use App\Models\Planilla;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;

class MostrarPlanillas extends Component
{
    use WithPagination;

    public $search         = '';
    public $searchDate     = null;
    public $isAdmin        = false;
    public $isSupervisor   = false;
    public $reloadPlanilla = 0;
    public $ultimoCambio   = null; // Para detectar cambios recientes
    public $searchMonth = null; 
    public $searchUser;

    public function mount()
    {
        $user               = auth()->user();
        $this->isAdmin      = $user && $user->role === 'admin';
        $this->isSupervisor = $user && $user->role === 'supervisor';

        // Guardar la fecha del último cambio al cargar
        $this->ultimoCambio = Planilla::latest('updated_at')->value('updated_at');
    }

    public function buscarPlanillas()
    {
        $this->resetPage();
    }

    protected $listeners = [
        'eliminarPlanilla',
        'reloadPlanilla' => '$refresh',
    ];

    public function eliminarPlanilla($id)
    {
        $planilla = Planilla::find($id);
        if ($planilla) {
            $planilla->delete();
            session()->flash('mensaje', 'Planilla eliminada correctamente ✅');
        }
        $this->reloadPlanilla++;
    }

    public function cambiarEstado(Planilla $planilla, $estado)
{
    // Verificar que solo supervisores puedan autorizar o negar
    if (auth()->user()->role !== 'supervisor') {
        abort(403, 'Solo supervisores pueden cambiar el estado de la planilla.');
    }

    $planilla->update([
        'estado_autorizacion' => $estado,
        'user_autoriza_id'    => auth()->id(), // Guardamos quién autorizó
    ]);

    // Notificación local
    $this->dispatch('notificacion-planilla', [
        'mensaje' => "Planilla marcada como " . ($estado === 'autorizado' ? 'Autorizada' : 'No autorizada'),
        'tipo'    => $estado === 'autorizado' ? 'success' : 'error',
    ]);

    $this->reloadPlanilla++;
}

    // Método que hace polling para detectar cambios de supervisores
    public function checkCambios()
    {
        if (! $this->isAdmin) {
            return;
        }
        // Solo para admin

        $ultimo = Planilla::latest('updated_at')->value('updated_at');

        if ($ultimo && $ultimo != $this->ultimoCambio) {
            $this->ultimoCambio = $ultimo;

            $planilla = Planilla::latest('updated_at')->first();

            // Solo notificar si el cambio lo hizo un supervisor
            if ($planilla->user->role === 'supervisor') {
                $this->dispatch('notificacion-planilla', [
                    'mensaje' => "El supervisor cambió la planilla {$planilla->numero_funcionario} a {$planilla->estado_autorizacion}",
                    'tipo'    => 'info',
                ]);
            }
        }
    }

    // MostrarPlanillas.php
public function imprimir($id)
{
    return redirect()->route('planillas.imprimir', $id);
}

    public function descargarPdf($id)
    {
        $planilla = Planilla::findOrFail($id);

        $pdf = Pdf::loadView('planillas.imprimir', compact('planilla'))
                  ->setPaper('a4');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "planilla-{$planilla->id}.pdf"
        );
    }

public function render()
{
    $planillas = Planilla::query()
        ->when(! in_array(auth()->user()->role, ['admin', 'supervisor']), function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('numero_funcionario', 'like', '%' . $this->search . '%')
                  ->orWhere('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('apellido', 'like', '%' . $this->search . '%')
                  ->orWhere('area', 'like', '%' . $this->search . '%'); // <-- Agregado
            });
        })
        ->when($this->searchDate, function ($query) {
            $query->whereDate('created_at', $this->searchDate);
        })
        ->when($this->searchMonth, function ($query) {
            $fecha = \Carbon\Carbon::createFromFormat('Y-m', $this->searchMonth);
            $query->whereMonth('fecha_inicio', $fecha->month)
                  ->whereYear('fecha_inicio', $fecha->year);
        })
        ->when($this->searchUser, function ($query) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->searchUser . '%');
            });
        })
        ->latest()
        ->paginate(10);

    return view('livewire.mostrar-planillas', [
        'planillas' => $planillas,
    ])->layout('layouts.app');
}


}
