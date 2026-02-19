<?php

namespace App\Livewire\Panol;

use App\Models\FueraServicio;
use App\Models\Funcionario;
use App\Models\Herramienta;
use App\Models\HerramientaPrestamo;
use App\Models\HistorialHerramienta;
use App\Models\Pedido;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

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
    protected $listeners = ['refreshHerramientas' => '$refresh'];



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
    'codigo' => $herramienta->codigo,       // Código de la herramienta
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

    $funcionario = Funcionario::find($this->funcionario_id);

    foreach ($this->prestamosMultiple as $herramientaId => $cantidad) {
        if ($cantidad <= 0) continue;

        $herramienta = Herramienta::find($herramientaId);
        if (!$herramienta) continue;

        if ($cantidad > $herramienta->cantidad_disponible) {
            session()->flash('error', "No hay suficiente cantidad de {$herramienta->nombre}");
            return;
        }

        // Crear préstamo
        HerramientaPrestamo::create([
            'herramienta_id' => $herramienta->id,
            'funcionario_id' => $this->funcionario_id,
            'cantidad' => $cantidad,
            'estado' => 'prestada',
        ]);

        // Actualizar stock
        $herramienta->cantidad_disponible -= $cantidad;
        $herramienta->cantidad_prestamo += $cantidad;
        $herramienta->save();

        // 🔥 HISTORIAL
        HistorialHerramienta::create([
            'herramienta_id' => $herramienta->id,
            'nombre' => $herramienta->nombre,
            'codigo' => $herramienta->codigo,
            'tipo' => 'prestamo_multiple',
            'cantidad' => $cantidad,
            'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
            'detalle' => 'Préstamo múltiple',
            'observacion' => null,
        ]);
    }

    $this->mostrarModalPrestamoMultiple = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Préstamos múltiples realizados correctamente');
}

// Guardar devolución múltiple
public function devolverHerramientasMultiple()
{
    if (!$this->funcionarioMultipleId) {
        session()->flash('error', 'Debes seleccionar un funcionario');
        return;
    }

    $funcionario = Funcionario::find($this->funcionarioMultipleId);

    foreach ($this->devolucionesMultiple as $prestamoId => $cantidad) {
        if ($cantidad <= 0) continue;

        $prestamo = HerramientaPrestamo::find($prestamoId);
        if (!$prestamo || $prestamo->estado != 'prestada') continue;

        // Cantidad real que se devuelve
        $cantidadDevuelta = $cantidad;

        if ($cantidad >= $prestamo->cantidad) {
            $cantidadDevuelta = $prestamo->cantidad;
            $prestamo->cantidad = 0;
            $prestamo->estado = 'devuelta';
        } else {
            $prestamo->cantidad -= $cantidad;
        }

        $prestamo->observaciones = $this->observacionesMultiple;
        $prestamo->save();

        $herramienta = $prestamo->herramienta;

        $herramienta->cantidad_disponible += $cantidadDevuelta;
        $herramienta->cantidad_prestamo -= $cantidadDevuelta;
        $herramienta->save();

        // HISTORIAL
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

    $this->mostrarModalDevolucionMultiple = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Devoluciones múltiples realizadas correctamente');
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
    if (!$this->funcionarioMultipleId) {
        $this->prestamosFuncionarioSeleccionado = [];
        $this->devolucionesMultiple = [];
        return;
    }

    $prestamos = HerramientaPrestamo::query()
    ->where('funcionario_id', $this->funcionarioMultipleId)
    ->where('estado', 'prestada')
    ->with([
        'herramienta:id,nombre'
    ])
    ->get();


    $this->prestamosFuncionarioSeleccionado = $prestamos;

    // inicializar inputs en 0
    $this->devolucionesMultiple = [];
    foreach ($prestamos as $prestamo) {
        $this->devolucionesMultiple[$prestamo->id] = 0;
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

    if (!$this->funcionario_id) {
        session()->flash('error', 'Debes seleccionar un funcionario');
        return;
    }

    if ($cantidad > $this->herramientaSeleccionada->cantidad_disponible) {
        session()->flash('error', 'No hay suficiente cantidad disponible');
        return;
    }

    // Registrar préstamo
    HerramientaPrestamo::create([
        'herramienta_id' => $this->herramientaSeleccionada->id,
        'funcionario_id' => $this->funcionario_id,
        'cantidad' => $cantidad,
        'estado' => 'prestada',
    ]);

    // Actualizar cantidades
    $this->herramientaSeleccionada->cantidad_disponible -= $cantidad;
    $this->herramientaSeleccionada->cantidad_prestamo += $cantidad;
    $this->herramientaSeleccionada->save();

        // 🔥 GUARDAR HISTORIAL
    $funcionario = Funcionario::find($this->funcionario_id);

    HistorialHerramienta::create([
        'herramienta_id' => $this->herramientaSeleccionada->id,
        'nombre' => $this->herramientaSeleccionada->nombre,
        'codigo' => $this->herramientaSeleccionada->codigo,
        'tipo' => 'prestamo',
        'cantidad' => $cantidad,
        'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
        'detalle' => 'Préstamo individual',
        'observacion' => null,
    ]);


    $this->mostrarModalPrestamo = false;
    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta prestada correctamente');
}

    // Abrir modal de devolución
   public function abrirModalDevolver(Herramienta $herramienta)
{
    $this->herramientaSeleccionada = $herramienta;
    $this->cantidadPrestamo = 1; // por defecto
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

    // Convertir IDs a array
    $prestamoIds = explode(',', $this->funcionarioPrestamoId);

    $cantidadADevolver = $this->cantidadPrestamo;

    // Traer los préstamos activos
    $prestamos = HerramientaPrestamo::whereIn('id', $prestamoIds)
        ->where('estado', 'prestada')
        ->orderBy('created_at')
        ->get();

    foreach ($prestamos as $prestamo) {

        if ($cantidadADevolver <= 0) break;

        if ($cantidadADevolver >= $prestamo->cantidad) {
            $cantidadDevuelta = $prestamo->cantidad;
            $cantidadADevolver -= $prestamo->cantidad;
            $prestamo->cantidad = 0;
            $prestamo->estado = 'devuelta';
        } else {
            $cantidadDevuelta = $cantidadADevolver;
            $prestamo->cantidad -= $cantidadADevolver;
            $cantidadADevolver = 0;
        }

        $prestamo->observaciones = $this->observacionesDevolucion;
        $prestamo->save();

        // 🔥 HISTORIAL POR CADA DEVOLUCIÓN
        $funcionario = Funcionario::find($prestamo->funcionario_id);

        HistorialHerramienta::create([
            'herramienta_id' => $prestamo->herramienta_id,
            'nombre' => $prestamo->herramienta->nombre,
            'codigo' => $prestamo->herramienta->codigo,
            'tipo' => 'devolucion',
            'cantidad' => $cantidadDevuelta,
            'funcionario' => $funcionario?->nombre . ' ' . $funcionario?->apellido,
            'detalle' => 'Devolución individual',
            'observacion' => $this->observacionesDevolucion,
        ]);
    }

    // Actualizar cantidades
    $this->herramientaSeleccionada->cantidad_disponible += $this->cantidadPrestamo;
    $this->herramientaSeleccionada->cantidad_prestamo -= $this->cantidadPrestamo;
    $this->herramientaSeleccionada->save();

    $this->mostrarModalDevolver = false;
    $this->cantidadPrestamo = 1;
    $this->funcionarioPrestamoId = null;
    $this->observacionesDevolucion = null;

    $this->dispatch('reload-page');
    session()->flash('success', 'Herramienta devuelta correctamente');
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
