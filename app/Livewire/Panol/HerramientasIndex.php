<?php

namespace App\Livewire\Panol;

use App\Models\Bateria;
use App\Models\FueraServicio;
use App\Models\Funcionario;
use App\Models\Herramienta;
use App\Models\HerramientaPrestamo;
use App\Models\HistorialHerramienta;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HerramientasIndex extends Component
{
    public $buscar = '';
    public $herramientaSeleccionada;
    public $cantidadPrestamo = 1;
    public $funcionario_id;
    public $mostrarModalPrestamo = false;
    public $mostrarModalDevolver = false;
    public $mostrarModalFueraServicio = false;
    public $mostrarPrestamos = [];
    public $mostrarFueraServicio = [];
    public $motivoFueraServicio;
    public $observacionesDevolucion = '';
    public $funcionarioPrestamoId;
    public $mostrarModalEliminar = false;
    public $mostrarModalEditar = false;
    public $nombreHerramienta;
    public $codigoHerramienta;
    public $cantidadHerramienta;
    public $filtroEstado = ''; 
    // Para préstamo múltiple
    public $mostrarModalPrestamoMultiple = false;
    public $prestamosMultiple = []; // ['herramienta_id' => cantidad]

    // Para devolución múltiple
    public $mostrarModalDevolucionMultiple = false;
    public $devolucionesMultiple = []; // ['prestamo_id' => cantidad]
    public $funcionarioMultipleId = null;
    public $observacionesMultiple = '';
    public $prestamosFuncionarioSeleccionado = [];
    public bool $historialAbierto = false;
    public $historial = [];
    // filtros historial
    public $filtroDesde;
    public $filtroHasta;
    public $filtroTipo;
    public $filtroBusqueda;
    public $filtroFuncionario;
    public bool $estadisticasAbiertas = false;
    public $mostrarModalPedidoHerramienta = false;
    public $cantidadPedidoHerramienta = 1;
    public $skuPedidoHerramienta;
    public $mostrarModalPedido = false;
    public $herramientaPedidoId;
    public $cantidadPedido;
    public $observacionPedido;
    public $skuPedido = null;
    public $cantidadBateriasPrestamo = 0;
    public $stockBaterias = 0;
    public $nuevoStockBaterias;
    public $bateriasMultiple = [];
    public $cantidadBateriasDevolucion = 0;
    public $bateriasDevolucionesMultiple = [];
    public $mostrarPrestamosBaterias = [];
    public $gciPedidoHerramienta;
    protected $listeners = ['refreshHerramientas' => '$refresh'];


public function togglePrestamosBaterias($herramientaId)
{
    if (isset($this->mostrarPrestamosBaterias[$herramientaId])) {
        unset($this->mostrarPrestamosBaterias[$herramientaId]);
    } else {
        $this->mostrarPrestamosBaterias[$herramientaId] = true;
    }
}

public function guardarStockBaterias()
{
    // Buscar la primera batería o crearla si no existe
    $bateria = Bateria::first();
    if (!$bateria) {
        $bateria = Bateria::create([
            'stock_total' => 0
        ]);
    }

    // Sumar o restar según el valor ingresado
    $bateria->stock_total += $this->nuevoStockBaterias;
    $bateria->save();

    // Actualizar variable para la vista
    $this->stockBaterias = $bateria->stock_total;

    // Limpiar input
    $this->nuevoStockBaterias = 0;

    $this->dispatch('reload-page');
    session()->flash('success', 'Stock de baterías actualizado correctamente.');
}

    /*
    |---------------------------------------------------
    | EXPORTAR COMO PDF
    |---------------------------------------------------
    */

    public function exportarEstadisticasHerramientasPdf()
{
    $stats = $this->obtenerEstadisticasHerramientas();

    $pdf = Pdf::loadView('pdf.estadisticas-herramientas', compact('stats'))
        ->setPaper('A4','landscape');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "estadisticas_herramientas_" . now()->format('Ymd_His') . ".pdf"
    );
}


    public function exportarHistorialHerramientasPdf()
{
    $historial = $this->historial;

    $pdf = Pdf::loadView('pdf.historial-herramientas', compact('historial'))
        ->setPaper('A4','landscape');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "historial_herramientas_" . now()->format('Ymd_His') . ".pdf"
    );
}

public function exportarHerramientasPdf()
{
    $herramientas = Herramienta::query()
        ->where(function($query){
            $query->where('nombre','like','%'.$this->buscar.'%')
                  ->orWhere('codigo','like','%'.$this->buscar.'%');
        })
        ->orderBy('nombre')
        ->get();

    $pdf = Pdf::loadView('pdf.herramientas', compact('herramientas'))
        ->setPaper('A4','landscape');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "herramientas_" . now()->format('Ymd_His') . ".pdf"
    );
}

   /*
    |---------------------------------------------------
    | EXPORTAR COMO CSV
    |---------------------------------------------------
    */


    public function exportarEstadisticasHerramientasCsv()
{
    $stats = $this->obtenerEstadisticasHerramientas();

    $filename = "estadisticas_herramientas_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($stats) {

        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // MAS USADAS
        fputcsv($file, ['HERRAMIENTAS MÁS USADAS'], ';');
        fputcsv($file, ['Nombre','Código','Cantidad total'], ';');
        foreach ($stats['mas_usadas'] as $s) {
            fputcsv($file, [$s->nombre, $s->codigo, $s->total], ';');
        }

        fputcsv($file, [], ';');

        // MENOS USADAS
        fputcsv($file, ['HERRAMIENTAS MENOS USADAS'], ';');
        fputcsv($file, ['Nombre','Código','Cantidad total'], ';');
        foreach ($stats['menos_usadas'] as $s) {
            fputcsv($file, [$s->nombre, $s->codigo, $s->total], ';');
        }

        fputcsv($file, [], ';');

        // FUNCIONARIOS
        fputcsv($file, ['FUNCIONARIOS QUE MÁS SOLICITAN'], ';');
        fputcsv($file, ['Funcionario','Cantidad total'], ';');
        foreach ($stats['funcionarios'] as $s) {
            fputcsv($file, [$s->funcionario, $s->total], ';');
        }

        fputcsv($file, [], ';');

        // USO MENSUAL
        fputcsv($file, ['USO MENSUAL'], ';');
        fputcsv($file, ['Mes','Cantidad total'], ';');
        foreach ($stats['uso_mensual'] as $s) {
            fputcsv($file, [$s->mes, $s->total], ';');
        }

        fclose($file);
    };

    return response()->stream($callback,200,$headers);
}


    public function exportarHistorialHerramientasCsv()
{
    $historial = $this->historial;

    $filename = "historial_herramientas_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($historial) {

        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'Fecha',
            'Herramienta',
            'Código',
            'Tipo',
            'Cantidad',
            'Funcionario',
            'Detalle',
            'Observación'
        ], ';');

        foreach ($historial as $h) {
            fputcsv($file, [
                $h->created_at->format('d/m/Y H:i'),
                $h->nombre,
                $h->codigo,
                $h->tipo,
                $h->cantidad,
                $h->funcionario,
                $h->detalle,
                $h->observacion
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback,200,$headers);
}


public function exportarHerramientasCsv()
{
    $herramientas = Herramienta::query()
        ->where(function($query){
            $query->where('nombre','like','%'.$this->buscar.'%')
                  ->orWhere('codigo','like','%'.$this->buscar.'%');
        })
        ->orderBy('nombre')
        ->get();

    $filename = "herramientas_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($herramientas) {

        $file = fopen('php://output', 'w');

        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'Nombre',
            'Código',
            'Cantidad total',
            'Disponible',
            'En préstamo',
            'Fuera servicio'
        ], ';');

        foreach ($herramientas as $h) {
            fputcsv($file, [
                $h->nombre,
                $h->codigo,
                $h->cantidad,
                $h->cantidad_disponible,
                $h->cantidad_prestamo,
                $h->cantidad_fuera_servicio
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback,200,$headers);
}


public function abrirModalPedido($herramientaId = null)
{
    $this->herramientaPedidoId = $herramientaId;
    $this->cantidadPedido = null;
    $this->observacionPedido = '';
    $this->mostrarModalPedido = true;
}

public function realizarPedido()
{
$this->validate([
    'herramientaPedidoId' => 'required|exists:herramientas,id',
    'cantidadPedido' => 'required|integer|min:1',
]);

$herramienta = Herramienta::find($this->herramientaPedidoId);

Pedido::create([
    'tipo' => 'herramienta',                // <- igual que 'material' pero con herramienta
    'herramientas_id' => $herramienta->id,  // ID de la herramienta
    'nombre' => $herramienta->nombre,       // Nombre de la herramienta
    'codigo' => $herramienta->codigo,      // Código de la herramienta
    'sku' => $this->skuPedido,              // SKU que venís usando
    'cantidad' => $this->cantidadPedido,    // Cantidad pedida
    'estado' => 'pendiente',                // Estado inicial

]);

    $this->mostrarModalPedido = false;
    $this->dispatch('reload-page');
     session()->flash('success', 'Pedido de herramienta generado correctamente');
}
public function abrirModalPedidoHerramienta(Herramienta $herramienta)
{
    $this->herramientaSeleccionada = $herramienta;
    $this->cantidadPedidoHerramienta = 1;
    $this->skuPedidoHerramienta = null;
    $this->gciPedidoHerramienta = $herramienta->gci;
    $this->mostrarModalPedidoHerramienta = true;
    
}

public function guardarPedidoHerramienta()
{
    $this->validate([
        'cantidadPedidoHerramienta' => 'required|integer|min:1'
    ]);

    Pedido::create([
        'tipo' => 'herramienta',
        'herramientas_id' => $this->herramientaSeleccionada->id,
        'nombre' => $this->herramientaSeleccionada->nombre,
        'codigo' => $this->herramientaSeleccionada->codigo,
        'gci' => $this->gciPedidoHerramienta,
        'sku' => $this->skuPedidoHerramienta,
        'cantidad' => $this->cantidadPedidoHerramienta,
        'estado' => 'pendiente'
    ]);

    $this->mostrarModalPedidoHerramienta = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Pedido de herramienta generado correctamente');
}

public function completarPedidoHerramienta($pedidoId)
{
    $pedido = Pedido::findOrFail($pedidoId);

    if ($pedido->estado == 'completado') return;

    $herramienta = Herramienta::find($pedido->herramientas_id);

    if ($herramienta) {
        $herramienta->cantidad_disponible += $pedido->cantidad;
        $herramienta->cantidad += $pedido->cantidad;
        $herramienta->save();
    }

    $pedido->estado = 'completado';
    $pedido->save();
    $this->dispatch('reload-page');
    session()->flash('success', 'Pedido completado y stock actualizado');
}

public function toggleEstadisticas()
{
    $this->estadisticasAbiertas = !$this->estadisticasAbiertas;
}


public function toggleHistorial()
{
    $this->historialAbierto = !$this->historialAbierto;
}

// Abrir modal de préstamo múltiple
public function abrirModalPrestamoMultiple()
{
    $this->prestamosMultiple = [];
    $this->funcionario_id = null;
    $this->mostrarModalPrestamoMultiple = true;
}

// Abrir modal de devolución múltiple
public function abrirModalDevolucionMultiple()
{
    $this->devolucionesMultiple = [];
    $this->bateriasDevolucionesMultiple = [];
    $this->funcionarioMultipleId = null;
    $this->observacionesMultiple = '';
    $this->mostrarModalDevolucionMultiple = true;
}

// Guardar préstamo múltiple
public function prestarHerramientasMultiple()
{
    if (!$this->funcionario_id) {
        session()->flash('error', 'Debes seleccionar un funcionario');
        return;
    }

    DB::transaction(function () {

        $funcionario = Funcionario::find($this->funcionario_id);
        $bateria = Bateria::first();

        // Contar baterías totales solicitadas
        $totalBateriasSolicitadas = 0;
        foreach ($this->prestamosMultiple as $herramientaId => $cantidad) {
            $herramienta = Herramienta::find($herramientaId);
            if (!$herramienta) continue;

            if ($herramienta->tipo_alimentacion === 'bateria') {
                $totalBateriasSolicitadas += $this->bateriasMultiple[$herramientaId] ?? 0;
            }
        }

        // Verificar stock global de baterías
        if ($bateria && $totalBateriasSolicitadas > $bateria->stock_total) {
            throw new \Exception("No hay suficientes baterías disponibles para todas las herramientas a batería.");
        }

        // Procesar préstamos
        foreach ($this->prestamosMultiple as $herramientaId => $cantidad) {

            if ($cantidad <= 0) continue;

            $herramienta = Herramienta::find($herramientaId);
            if (!$herramienta) continue;

            if ($cantidad > $herramienta->cantidad_disponible) {
                throw new \Exception("No hay suficiente cantidad de {$herramienta->nombre}");
            }

            $cantidadBaterias = 0;

            // Si la herramienta es a batería
            if ($herramienta->tipo_alimentacion === 'bateria') {
                $cantidadBaterias = $this->bateriasMultiple[$herramientaId] ?? 0;

                if ($cantidadBaterias > 0 && $bateria) {
                    $bateria->decrement('stock_total', $cantidadBaterias);
                }
            }

            // Crear préstamo
            HerramientaPrestamo::create([
                'herramienta_id' => $herramienta->id,
                'funcionario_id' => $this->funcionario_id,
                'cantidad' => $cantidad,
                'cantidad_baterias' => $cantidadBaterias,
                'estado' => 'prestada',
            ]);

            // Actualizar herramienta
            $herramienta->decrement('cantidad_disponible', $cantidad);
            $herramienta->increment('cantidad_prestamo', $cantidad);

            // Historial
            HistorialHerramienta::create([
                'herramienta_id' => $herramienta->id,
                'nombre' => $herramienta->nombre,
                'codigo' => $herramienta->codigo,
                'tipo' => 'prestamo_multiple',
                'cantidad' => $cantidad,
                'cantidad_baterias' => $cantidadBaterias,
                'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
                'detalle' => 'Préstamo múltiple',
                'observacion' => null,
            ]);
        }

    });

    $this->mostrarModalPrestamoMultiple = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Préstamos múltiples realizados correctamente');
}
// Guardar devolución múltiple
public function devolverHerramientasMultiple()
{
    $funcionario = Funcionario::find($this->funcionarioMultipleId);

    foreach ($this->devolucionesMultiple as $prestamoId => $cantidadHerramientas) {

        $prestamo = HerramientaPrestamo::with('herramienta')->find($prestamoId);
        if (!$prestamo) continue;

        $herramienta = $prestamo->herramienta;

        $cantidadBaterias = $this->bateriasDevolucionesMultiple[$prestamoId] ?? 0;

        //  Si no devuelve nada, saltar
        if ($cantidadHerramientas <= 0 && $cantidadBaterias <= 0) {
            continue;
        }

        /*
        ==========================================
        DEVOLVER HERRAMIENTAS
        ==========================================
        */
        $cantidadDevuelta = min($cantidadHerramientas, $prestamo->cantidad);

        if ($cantidadDevuelta > 0) {

            $prestamo->cantidad -= $cantidadDevuelta;

            // Actualizar stock herramienta
            $herramienta->increment('cantidad_disponible', $cantidadDevuelta);
            $herramienta->decrement('cantidad_prestamo', $cantidadDevuelta);

            // Historial
            HistorialHerramienta::create([
                'herramienta_id' => $herramienta->id,
                'nombre' => $herramienta->nombre,
                'codigo' => $herramienta->codigo,
                'tipo' => 'devolucion_multiple',
                'cantidad' => $cantidadDevuelta,
                'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
                'detalle' => 'Devolución múltiple de herramienta',
                'observacion' => $this->observacionesMultiple,
            ]);
        }

        /*
        ==========================================
        DEVOLVER BATERÍAS
        ==========================================
        */
        if ($herramienta->tipo_alimentacion === 'bateria') {

            $bateria = Bateria::first();

            if ($bateria && $cantidadBaterias > 0) {

                $cantidadBaterias = min(
                    $cantidadBaterias,
                    $prestamo->cantidad_baterias
                );

                $prestamo->cantidad_baterias -= $cantidadBaterias;

                $bateria->increment('stock_total', $cantidadBaterias);
            }
        }

        /*
        ==========================================
        CERRAR PRÉSTAMO SOLO SI NO QUEDA NADA
        ==========================================
        */
        if (
            $prestamo->cantidad <= 0 &&
            $prestamo->cantidad_baterias <= 0
        ) {
            $prestamo->estado = 'devuelta';
        } else {
            $prestamo->estado = 'prestada';
        }

        $prestamo->observaciones = $this->observacionesMultiple;
        $prestamo->save();
    }
// 🔄 Refrescar lista del funcionario
$this->updatedFuncionarioMultipleId();

// Cerrar modal y resetear campos
$this->mostrarModalDevolucionMultiple = false;
$this->funcionarioMultipleId = null;
$this->devolucionesMultiple = [];
$this->bateriasDevolucionesMultiple = [];
$this->observacionesMultiple = '';

// Refrescar página (si querés)
$this->dispatch('reload-page');

// Mensaje de éxito
session()->flash('success', 'Devolución múltiple realizada correctamente.');
}



public function obtenerEstadisticasHerramientas()
{
    return [

        // MÁS USADAS
        'mas_usadas' => HistorialHerramienta::selectRaw('nombre, codigo, SUM(cantidad) as total')
            ->where('tipo','prestamo')
            ->groupBy('nombre','codigo')
            ->orderByDesc('total')
            ->take(10)
            ->get(),

        // MENOS USADAS
        'menos_usadas' => HistorialHerramienta::selectRaw('nombre, codigo, SUM(cantidad) as total')
            ->where('tipo','prestamo')
            ->groupBy('nombre','codigo')
            ->orderBy('total')
            ->take(10)
            ->get(),

        // FUNCIONARIOS QUE MÁS PIDEN
        'funcionarios' => HistorialHerramienta::selectRaw('funcionario, SUM(cantidad) as total')
            ->where('tipo','prestamo')
            ->groupBy('funcionario')
            ->orderByDesc('total')
            ->take(10)
            ->get(),

        // USO MENSUAL
        'uso_mensual' => HistorialHerramienta::selectRaw('DATE_FORMAT(created_at,"%Y-%m") as mes, SUM(cantidad) as total')
            ->where('tipo','prestamo')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get(),
    ];
}


public function updatedFuncionarioMultipleId()
{
    // Reset si no hay funcionario
    if (!$this->funcionarioMultipleId) {
        $this->prestamosFuncionarioSeleccionado = [];
        $this->devolucionesMultiple = [];
        $this->bateriasDevolucionesMultiple = [];
        return;
    }

    // Traer préstamos activos (cantidad > 0 o cantidad_baterias > 0)
    $prestamos = HerramientaPrestamo::with('herramienta')
        ->where('funcionario_id', $this->funcionarioMultipleId)
        ->where(function($q){
            $q->where('cantidad', '>', 0)
              ->orWhere('cantidad_baterias', '>', 0);
        })
        ->get();

    $this->prestamosFuncionarioSeleccionado = $prestamos;

    // Reset arrays de inputs
    $this->devolucionesMultiple = [];
    $this->bateriasDevolucionesMultiple = [];

    foreach ($prestamos as $prestamo) {

        // Solo agregar si queda cantidad de herramientas
        if ($prestamo->cantidad > 0) {
            $this->devolucionesMultiple[$prestamo->id] = 0;
        }

        // Solo agregar si tipo batería y quedan baterías prestadas
        if (
            strtolower($prestamo->herramienta->tipo_alimentacion) === 'bateria' &&
            $prestamo->cantidad_baterias > 0
        ) {
            $this->bateriasDevolucionesMultiple[$prestamo->id] = 0;
        }
    }
}
    // Abrir modal
public function abrirModalEditar(Herramienta $herramienta)
{
    $this->herramientaSeleccionada = $herramienta;
    $this->nombreHerramienta = $herramienta->nombre;
    $this->codigoHerramienta = $herramienta->codigo;
    $this->cantidadHerramienta = $herramienta->cantidad;
    $this->mostrarModalEditar = true;
}

// Guardar cambios
public function actualizarHerramienta()
{
    $this->validate([
        'nombreHerramienta' => 'required|string|max:255',
        'codigoHerramienta' => 'required|string|max:100',
        'cantidadHerramienta' => 'required|integer|min:0',
    ]);

    // Actualizar herramienta
    $this->herramientaSeleccionada->nombre = $this->nombreHerramienta;
    $this->herramientaSeleccionada->codigo = $this->codigoHerramienta;
    
    // Si se cambia la cantidad total, ajustar también la cantidad disponible
    $diferencia = $this->cantidadHerramienta - $this->herramientaSeleccionada->cantidad;
    $this->herramientaSeleccionada->cantidad += $diferencia;
    $this->herramientaSeleccionada->cantidad_disponible += $diferencia;

    $this->herramientaSeleccionada->save();

    $this->mostrarModalEditar = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta actualizada correctamente');
}

public function abrirModalEliminar(Herramienta $herramienta)
{
    $this->herramientaSeleccionada = $herramienta;
    $this->mostrarModalEliminar = true;
}

public function confirmarEliminarHerramienta()
{
    $this->herramientaSeleccionada->delete();
    $this->mostrarModalEliminar = false;
    $this->herramientaSeleccionada = null;
    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta eliminada correctamente');
}

    // Abrir modal de préstamo
    public function abrirModalPrestamo(Herramienta $herramienta)
    {
        $this->herramientaSeleccionada = $herramienta;
        $this->cantidadPrestamo = 1;
        $this->funcionario_id = null;
        $this->mostrarModalPrestamo = true;
    }

    // Función para abrir/cerrar el toggle
public function togglePrestamos($herramientaId)
{
    if(isset($this->mostrarPrestamos[$herramientaId])) {
        unset($this->mostrarPrestamos[$herramientaId]);
    } else {
        $this->mostrarPrestamos[$herramientaId] = true;
    }
}

public function prestarHerramienta()
{
    $cantidad = $this->cantidadPrestamo;
    $cantidadBaterias = $this->cantidadBateriasPrestamo; // ← guardar copia

    if (!$this->funcionario_id) {
        session()->flash('error', 'Debes seleccionar un funcionario');
        return;
    }

    if ($cantidad > $this->herramientaSeleccionada->cantidad_disponible) {
        session()->flash('error', 'No hay suficiente cantidad disponible');
        return;
    }

    if ($this->herramientaSeleccionada->tipo_alimentacion === 'bateria') {

        $bateria = Bateria::first();

        if (!$bateria) {
            session()->flash('error', 'No existe registro de baterías.');
            return;
        }

        if ($cantidadBaterias > $bateria->stock_total) {
            session()->flash('error', 'No hay suficientes baterías disponibles.');
            return;
        }

        $bateria->decrement('stock_total', $cantidadBaterias);
    }

    // Crear préstamo
    HerramientaPrestamo::create([
        'herramienta_id' => $this->herramientaSeleccionada->id,
        'funcionario_id' => $this->funcionario_id,
        'cantidad' => $cantidad,
        'cantidad_baterias' => $cantidadBaterias,
        'estado' => 'prestada',
    ]);

    // Actualizar stock herramienta
    $this->herramientaSeleccionada->cantidad_disponible -= $cantidad;
    $this->herramientaSeleccionada->cantidad_prestamo += $cantidad;
    $this->herramientaSeleccionada->save();

    $funcionario = Funcionario::find($this->funcionario_id);

    // 🔥 HISTORIAL CORRECTO
    HistorialHerramienta::create([
        'herramienta_id' => $this->herramientaSeleccionada->id,
        'nombre' => $this->herramientaSeleccionada->nombre,
        'codigo' => $this->herramientaSeleccionada->codigo,
        'tipo' => 'prestamo',
        'cantidad' => $cantidad,
        'cantidad_baterias' => $cantidadBaterias,
        'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
        'detalle' => 'Préstamo individual',
        'observacion' => null,
    ]);

    // recién acá reseteás
    $this->cantidadBateriasPrestamo = 0;

    $this->mostrarModalPrestamo = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta prestada correctamente');
}

    // Abrir modal de devolución
   public function abrirModalDevolver(Herramienta $herramienta)
{
    $this->herramientaSeleccionada = $herramienta;
    $this->cantidadPrestamo = 1; // por defecto
    $this->cantidadBateriasDevolucion = 0;
    $this->funcionarioPrestamoId = null;
    $this->observacionesDevolucion = '';
    $this->mostrarModalDevolver = true;
}



public function devolverHerramienta()
{
    if (!$this->funcionarioPrestamoId) {
        session()->flash('error', 'Debes seleccionar un funcionario');
        return;
    }

    $prestamoIds = explode(',', $this->funcionarioPrestamoId);
    $cantidadADevolver = $this->cantidadPrestamo;
    $cantidadBateriasADevolver = $this->cantidadBateriasDevolucion;

    $prestamos = HerramientaPrestamo::with('herramienta')
        ->whereIn('id', $prestamoIds)
        ->where('estado', 'prestada')
        ->orderBy('created_at')
        ->get();

    $funcionario = Funcionario::find($prestamos->first()?->funcionario_id);

    $bateria = Bateria::first();

    foreach ($prestamos as $prestamo) {

        // -----------------------------
        // Devolver herramientas
        // -----------------------------
        if ($cantidadADevolver > 0 && $prestamo->cantidad > 0) {
            $cantidadDevuelta = min($cantidadADevolver, $prestamo->cantidad);
            $prestamo->cantidad -= $cantidadDevuelta;
            $cantidadADevolver -= $cantidadDevuelta;

            $prestamo->estado = ($prestamo->cantidad + $prestamo->cantidad_baterias <= 0) ? 'devuelta' : 'prestada';

            // Actualizar stock herramienta
            $prestamo->herramienta->increment('cantidad_disponible', $cantidadDevuelta);
            $prestamo->herramienta->decrement('cantidad_prestamo', $cantidadDevuelta);

            // Historial herramienta
            HistorialHerramienta::create([
                'herramienta_id' => $prestamo->herramienta_id,
                'nombre' => $prestamo->herramienta->nombre,
                'codigo' => $prestamo->herramienta->codigo,
                'tipo' => 'devolucion',
                'cantidad' => $cantidadDevuelta,
                'cantidad_baterias' => 0,
                'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
                'detalle' => 'Devolución herramienta',
                'observacion' => $this->observacionesDevolucion,
            ]);
        }

        // -----------------------------
        // Devolver baterías dentro del mismo préstamo
        // -----------------------------
        if ($cantidadBateriasADevolver > 0 && $prestamo->herramienta->tipo_alimentacion === 'bateria' && $prestamo->cantidad_baterias > 0) {
            $cantidadBateriasDevuelta = min($cantidadBateriasADevolver, $prestamo->cantidad_baterias);
            $prestamo->cantidad_baterias -= $cantidadBateriasDevuelta;
            $cantidadBateriasADevolver -= $cantidadBateriasDevuelta;

            // Actualizar stock global de baterías
            if ($bateria) {
                $bateria->increment('stock_total', $cantidadBateriasDevuelta);
            }

            // Historial batería
            HistorialHerramienta::create([
                'herramienta_id' => $prestamo->herramienta_id,
                'nombre' => $prestamo->herramienta->nombre,
                'codigo' => $prestamo->herramienta->codigo,
                'tipo' => 'devolucion',
                'cantidad' => 0,
                'cantidad_baterias' => $cantidadBateriasDevuelta,
                'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
                'detalle' => 'Devolución batería',
                'observacion' => $this->observacionesDevolucion,
            ]);

            // Actualizar estado del préstamo original
            $prestamo->estado = ($prestamo->cantidad + $prestamo->cantidad_baterias <= 0) ? 'devuelta' : 'prestada';
        }

        // Guardar observaciones del préstamo original
        $prestamo->observaciones = $this->observacionesDevolucion;
        $prestamo->save();
    }

    // Reset y cerrar modal
    $this->mostrarModalDevolver = false;
    $this->cantidadPrestamo = 1;
    $this->funcionarioPrestamoId = null;
    $this->cantidadBateriasDevolucion = 0;
    $this->observacionesDevolucion = null;

    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta y/o batería devuelta correctamente');
}
    // Abrir modal fuera de servicio
   public function abrirModalFueraServicio(Herramienta $herramienta)
{
    $this->herramientaSeleccionada = $herramienta;
    $this->cantidadPrestamo = 1; // por defecto
    $this->motivoFueraServicio = '';
    $this->mostrarModalFueraServicio = true;
}

public function toggleFueraServicio($herramientaId)
{
    if(isset($this->mostrarFueraServicio[$herramientaId])) {
        unset($this->mostrarFueraServicio[$herramientaId]);
    } else {
        $this->mostrarFueraServicio[$herramientaId] = true;
    }
}

public function marcarFueraServicio()
{
    $cantidad = $this->cantidadPrestamo;

    if ($cantidad > $this->herramientaSeleccionada->cantidad_disponible) {
        session()->flash('error', 'No hay suficientes herramientas disponibles');
        return;
    }

    // Reducir cantidad disponible y aumentar fuera de servicio
    $this->herramientaSeleccionada->cantidad_disponible -= $cantidad;
    $this->herramientaSeleccionada->cantidad_fuera_servicio += $cantidad;
    $this->herramientaSeleccionada->save();

    // Guardar registro con motivo
    FueraServicio::create([
        'herramienta_id' => $this->herramientaSeleccionada->id,
        'cantidad' => $cantidad,
        'motivo' => $this->motivoFueraServicio,
    ]);

    // 🔥 HISTORIAL
    HistorialHerramienta::create([
        'herramienta_id' => $this->herramientaSeleccionada->id,
        'nombre' => $this->herramientaSeleccionada->nombre,
        'codigo' => $this->herramientaSeleccionada->codigo,
        'tipo' => 'fuera_servicio',
        'cantidad' => $cantidad,
        'funcionario' => null,
        'detalle' => 'Marcada como fuera de servicio',
        'observacion' => $this->motivoFueraServicio,
    ]);

    $this->mostrarModalFueraServicio = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta marcada como fuera de servicio');
}

public function restaurarFueraServicio($id)
{
    // Buscar el registro específico
    $fuera = FueraServicio::find($id);

    if(!$fuera) {
        session()->flash('error', 'Registro no encontrado');
        return;
    }

    $herramienta = $fuera->herramienta;

    // Sumar la cantidad a la herramienta
    $herramienta->cantidad_disponible += $fuera->cantidad;
    $herramienta->cantidad_fuera_servicio -= $fuera->cantidad;
    $herramienta->save();

    // 🔥 HISTORIAL
    HistorialHerramienta::create([
        'herramienta_id' => $herramienta->id,
        'nombre' => $herramienta->nombre,
        'codigo' => $herramienta->codigo,
        'tipo' => 'restaurado',
        'cantidad' => $fuera->cantidad,
        'funcionario' => null,
        'detalle' => 'Restaurado desde fuera de servicio',
        'observacion' => $fuera->motivo, // opcional, para saber por qué estaba fuera
    ]);

    // Eliminar el registro de fuera de servicio
    $fuera->delete();

    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta restaurada correctamente');
}

    // Eliminar herramienta
    public function eliminarHerramienta(Herramienta $herramienta)
    {
        $herramienta->delete();
        session()->flash('success', 'Herramienta eliminada');
    }

public function render()
{
    // ======================
    // HISTORIAL HERRAMIENTAS
    // ======================

    $this->historial = HistorialHerramienta::query()
        ->when($this->filtroDesde, fn($q) =>
            $q->whereDate('created_at', '>=', \Carbon\Carbon::parse($this->filtroDesde)->startOfDay())
        )
        ->when($this->filtroHasta, fn($q) =>
            $q->whereDate('created_at', '<=', \Carbon\Carbon::parse($this->filtroHasta)->endOfDay())
        )
        ->when($this->filtroTipo, fn($q) =>
            $q->where('tipo', $this->filtroTipo)
        )
        ->when($this->filtroBusqueda, fn($q) =>
            $q->where(function ($q2) {
                $q2->where('nombre', 'like', '%'.$this->filtroBusqueda.'%')
                   ->orWhere('codigo', 'like', '%'.$this->filtroBusqueda.'%');
            })
        )
        ->when($this->filtroFuncionario, fn($q) =>
            $q->where('funcionario', 'like', '%'.$this->filtroFuncionario.'%')
        )
        ->orderBy('created_at', 'desc')
        ->get();


    // ======================
    // HERRAMIENTAS
    // ======================

    $herramientas = Herramienta::with('prestamos.funcionario')
        ->where(function($query){
            $query->where('nombre', 'like', '%'.$this->buscar.'%')
                  ->orWhere('codigo', 'like', '%'.$this->buscar.'%');
        });

    if($this->filtroEstado == 'disponible') {
        $herramientas->where('cantidad_disponible', '>', 0);
    } elseif($this->filtroEstado == 'prestamo') {
        $herramientas->where('cantidad_prestamo', '>', 0);
    } elseif($this->filtroEstado == 'fuera_servicio') {
        $herramientas->where('cantidad_fuera_servicio', '>', 0);
    }

    $herramientas = $herramientas->orderBy('nombre')->get();

    $funcionarios = Funcionario::orderBy('nombre')->get();

    // ======================
// ESTADISTICAS
// ======================

$herramientasMasUsadas = collect();
$herramientasMenosUsadas = collect();
$funcionariosUso = collect();
$usoPorMes = collect();

if ($this->estadisticasAbiertas) {

    $herramientasMasUsadas = HistorialHerramienta::selectRaw('herramienta_id, nombre, SUM(cantidad) as total_usos')
        ->whereIn('tipo', ['prestamo', 'prestamo_multiple'])
        ->groupBy('herramienta_id', 'nombre')
        ->orderByDesc('total_usos')
        ->take(10)
        ->get();

    $herramientasMenosUsadas = HistorialHerramienta::selectRaw('herramienta_id, nombre, SUM(cantidad) as total_usos')
        ->whereIn('tipo', ['prestamo', 'prestamo_multiple'])
        ->groupBy('herramienta_id', 'nombre')
        ->orderBy('total_usos')
        ->take(10)
        ->get();

    $funcionariosUso = HistorialHerramienta::selectRaw('funcionario, SUM(cantidad) as total')
        ->whereIn('tipo', ['prestamo', 'prestamo_multiple'])
        ->whereNotNull('funcionario')
        ->groupBy('funcionario')
        ->orderByDesc('total')
        ->get();

    $usoPorMes = HistorialHerramienta::selectRaw('MONTH(created_at) as mes, SUM(cantidad) as total')
        ->whereIn('tipo', ['prestamo', 'prestamo_multiple'])
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();
}

$pedidosHerramientas = Pedido::where('tipo', 'herramienta')
    ->where('estado', 'pendiente')
    ->with('herramienta')
    ->orderByDesc('created_at')
    ->get();
$this->stockBaterias = Bateria::first()?->stock_total ?? 0;

return view('livewire.panol.herramientas-index', compact(
    'herramientas',
    'funcionarios',
    'herramientasMasUsadas',
    'herramientasMenosUsadas',
    'funcionariosUso',
    'usoPorMes'
))->layout('layouts.app');
}



}
