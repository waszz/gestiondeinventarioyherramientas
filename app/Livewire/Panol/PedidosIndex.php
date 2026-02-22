<?php

namespace App\Livewire\Panol;

use App\Models\Herramienta;
use App\Models\Material;
use App\Models\Pedido;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidosIndex extends Component
{
    public $buscar = '';
    public $filtroEstado = '';
    public $filtroTipo = '';
    public $nombre;
    public $codigo;
    public $sku;
    public $numero_seguimiento;
    public $cantidad = 1;
    public $tipo = 'material';
    public $mostrarModalCrear = false;
    public $mostrarModalMail = false;
    public $emailDestino = '';
    public $pedidoSeleccionadoId;
    public $herramientaPedidoId; // id de la herramienta a pedir
    public $cantidadPedido = 1;  // cantidad a pedir
    public $observacionPedido;
    public $historialActivo = 'completados'; // Por defecto muestra completados
    public $mostrarHistorial = false; // toggle general para mostrar/ocultar historial
    public $filtroNombre = '';
    public $filtroSeguimiento = '';
    public $tipoPedido; // 'herramienta_existente', 'herramienta_nueva', 'material_existente', 'material_nuevo'
    public $herramientaSeleccionadaId;
    public $materialSeleccionadoId;
    public $nombreNuevo;
    public $codigoNuevo;
    public $cantidadNuevo = 1;
    public $skuNuevo;
    public $mostrarModalRechazo = false;
    public $pedidoSeleccionado = null;
    public $stockMinimoNuevo;
    // Listados existentes para selects
    public $herramientas;
    public $materiales;
    public $gciNuevo;
    public $gciPedidoHerramienta;
    public $tipoHerramientaNuevo; 
    protected $listeners = ['refreshPepidos' => '$refresh'];

    
public function mount()
{
    $this->herramientas = Herramienta::all();
    $this->materiales = Material::all();
}



    /*
    |---------------------------------------------------
    | EXPORTAR COMO PDF
    |---------------------------------------------------
    */

    public function exportarPedidosPdf($estado)
{
    $pedidos = Pedido::where('estado', $estado)
        ->when($this->filtroNombre, fn($q) => $q->where('nombre','like','%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento','like','%'.$this->filtroSeguimiento.'%'))
        ->orderBy('created_at','desc')
        ->get();

    $pdf = Pdf::loadView('pdf.pedidos', [
        'pedidos' => $pedidos,
        'estado' => $estado
    ])->setPaper('A4','landscape');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "pedidos_{$estado}_" . now()->format('Ymd_His') . ".pdf"
    );
}

   /*
    |---------------------------------------------------
    | EXPORTAR COMO CSV
    |---------------------------------------------------
    */

    public function exportarPedidosCsv($estado)
{
    $pedidos = Pedido::where('estado', $estado)
        ->when($this->filtroNombre, fn($q) => $q->where('nombre','like','%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento','like','%'.$this->filtroSeguimiento.'%'))
        ->orderBy('created_at','desc')
        ->get();

    $filename = "pedidos_{$estado}_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($pedidos, $estado) {

        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, ["PEDIDOS " . strtoupper($estado)], ';');

        fputcsv($file, [
            'Fecha',
            'Tipo',
            'Nombre',
            'Código',
            'SKU',
            'Cantidad',
            'Estado',
            'Seguimiento',
            'Observación'
        ], ';');

        foreach ($pedidos as $p) {
            fputcsv($file, [
                $p->created_at->format('d/m/Y H:i'),
                $p->tipo,
                $p->nombre,
                $p->codigo,
                $p->sku,
                $p->cantidad,
                $p->estado,
                $p->numero_seguimiento,
                $p->observacion
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback,200,$headers);
}


public function cambiarHistorial($tipo)
{
    if (in_array($tipo, ['completados', 'rechazados'])) {
        $this->historialActivo = $tipo;
    }
}

   public function guardarPedidoHerramienta()
{
    $this->validate([
        'herramientaPedidoId' => 'required|exists:herramientas,id',
        'cantidadPedido' => 'required|integer|min:1',
    ]);

    $herramienta = Herramienta::findOrFail($this->herramientaPedidoId);

    if ($this->cantidadPedido > $herramienta->cantidad_disponible) {
        $this->addError('cantidadPedido', 'No hay suficiente cantidad disponible.');
        return;
    }

    // Crear pedido usando el GCI de la herramienta
    Pedido::create([
        'tipo' => 'herramienta',
        'herramientas_id' => $herramienta->id,
        'nombre' => $herramienta->nombre,
        'codigo' => $herramienta->codigo,
        'sku' => $herramienta->sku,
        'cantidad' => $this->cantidadPedido,
        'estado' => 'pendiente',
        'observacion' => $this->observacionPedido,
        'numero_seguimiento' => $herramienta->gci_codigo, 
    ]);

    // Restar cantidad disponible
    $herramienta->cantidad_disponible -= $this->cantidadPedido;
    $herramienta->save();

    // Limpiar campos
    $this->herramientaPedidoId = null;
    $this->cantidadPedido = 1;
    $this->observacionPedido = null;

    session()->flash('success', 'Pedido de herramienta realizado correctamente');
    $this->dispatch('reload-page'); // recarga para actualizar tabla
}


public function abrirModalMail($pedidoId)
{
    $pedido = Pedido::find($pedidoId);
    if (!$pedido) {
        session()->flash('error', 'Pedido no encontrado');
        return;
    }

    $this->pedidoSeleccionadoId = $pedidoId;
    $this->emailDestino = '';
    $this->mostrarModalMail = true;
}

public function enviarMail()
{
    $this->validate([
        'emailDestino' => 'required|email',
    ]);

    $pedido = Pedido::find($this->pedidoSeleccionadoId);
    if (!$pedido) return;

    $asunto = urlencode("Pedido: {$pedido->nombre}");
    $cuerpo = urlencode("Detalles del pedido:\n\nNombre: {$pedido->nombre}\nCantidad: {$pedido->cantidad}\nTipo: {$pedido->tipo}\nEstado: {$pedido->estado}");

    $mailto = "mailto:{$this->emailDestino}?subject={$asunto}&body={$cuerpo}";

    // Dispatch Livewire evento que el navegador escucha
    $this->dispatch('abrir-mail', ['mailto' => $mailto]);

    // Cierra modal
    $this->mostrarModalMail = false;
}
    // =========================
    // CREAR PEDIDO
    // =========================

    public function abrirModalCrear()
{
    $this->reset([
        'nombre',
        'codigo',
        'sku',
        'numero_seguimiento',
        'cantidad',
        'tipo',
        'gciNuevo',
    ]);

    $this->cantidad = 1;
    $this->tipo = 'material';

    $this->mostrarModalCrear = true;
}

  public function guardarPedido()
{
  // Validaciones dinámicas

if (in_array($this->tipoPedido, ['herramienta_nueva', 'material_nuevo'])) {
    $this->validate([
        'nombreNuevo' => 'required|string|max:255',
        'codigoNuevo' => 'nullable|string|max:255',
        'cantidadNuevo' => 'required|integer|min:1',
    ]);

    // SOLO si es material nuevo
    if ($this->tipoPedido === 'material_nuevo') {
        $this->validate([
            'stockMinimoNuevo' => 'required|integer|min:0'
        ]);
    }

    // SOLO si es herramienta nueva -> validar tipo
    if ($this->tipoPedido === 'herramienta_nueva') {
        $this->validate([
            'tipoHerramientaNuevo' => 'required|string|in:cable,bateria,no_aplica',
        ]);
    }
}
    

    // Determinar datos a guardar según tipo
    $data = [
        'cantidad' => $this->cantidadNuevo,
        'estado' => 'pendiente',
        'sku' => $this->skuNuevo ?? null,
        'numero_seguimiento' => $this->numero_seguimiento ?? null, 
    ];

    switch ($this->tipoPedido) {
        case 'herramienta_existente':
            $herr = Herramienta::findOrFail($this->herramientaSeleccionadaId);
            $data = array_merge($data, [
                'tipo' => 'herramienta',
                'nombre' => $herr->nombre,
                'codigo' => $herr->codigo,
                'herramientas_id' => $herr->id,
                'numero_seguimiento' => $herr->gci_codigo, 
                'tipo_alimentacion' => $herr->tipo_alimentacion,
            ]);
            break;

        case 'material_existente':
    $mat = Material::findOrFail($this->materialSeleccionadoId);
    $data = array_merge($data, [
        'tipo' => 'material',
        'nombre' => $mat->nombre,
        'codigo' => $mat->codigo,
        'materiales_id' => $mat->id,
        'numero_seguimiento' => $mat->gci_codigo, // <- aquí
    ]);
    break;

        case 'herramienta_nueva':
            $data = array_merge($data, [
                'tipo' => 'herramienta',
                'nombre' => $this->nombreNuevo,
                'codigo' => $this->codigoNuevo,
                'numero_seguimiento' => $this->gciNuevo,
                'tipo_alimentacion' => $this->tipoHerramientaNuevo,
            ]);
            break;

       case 'material_nuevo':
    $data = array_merge($data, [
        'tipo' => 'material',
        'nombre' => $this->nombreNuevo,
        'codigo' => $this->codigoNuevo ?? 'N/A',
        'stock_minimo' => $this->stockMinimoNuevo,
        'numero_seguimiento' => $this->gciNuevo,
    ]);
    break;
    }

    // Crear pedido
    Pedido::create($data);

    // Resetear campos y cerrar modal
    $this->reset([
        'tipoPedido', 'herramientaSeleccionadaId', 'materialSeleccionadoId',
        'nombreNuevo', 'codigoNuevo', 'cantidadNuevo', 'skuNuevo', 'numero_seguimiento',
        'mostrarModalCrear',
        'stockMinimoNuevo',
    ]);
            $this->dispatch('reload-page');
    session()->flash('success', 'Pedido creado correctamente');
}


    // =========================
    // ACCIONES
    // =========================

    public function marcarCompletado($id)
    {
        Pedido::find($id)?->update([
            'estado' => 'completado'
        ]);
    }

   public function confirmarRechazo($id)
{
    $this->pedidoSeleccionado = $id;
    $this->mostrarModalRechazo = true;
}

public function rechazarPedido()
{
    if ($this->pedidoSeleccionado) {
        Pedido::find($this->pedidoSeleccionado)?->update([
            'estado' => 'rechazado'
        ]);

        // Reiniciar modal
        $this->pedidoSeleccionado = null;
        $this->mostrarModalRechazo = false;
        $this->dispatch('reload-page');
        session()->flash('success', 'Pedido rechazado correctamente.');
    }
}

public function cancelarRechazo()
{
    $this->pedidoSeleccionado = null;
    $this->mostrarModalRechazo = false;
}

public function completarPedido($pedidoId)
{
    $pedido = Pedido::findOrFail($pedidoId);

    // Evitar completar dos veces
    if ($pedido->estado === 'completado') {
        return;
    }

    // ============================
    // Manejo de materiales
    // ============================
    if ($pedido->tipo === 'material') {
        if ($pedido->materiales_id) {
            // Material existente: sumar stock
            $material = Material::find($pedido->materiales_id);
            if ($material) {
                $material->stock_actual += $pedido->cantidad;
                $material->save();
            }
        } else {
            // Material nuevo: crear registro
            $material = Material::create([
                'nombre' => $pedido->nombre,
                'codigo_referencia' => $pedido->codigo ?? 'N/A',
                'sku' => $pedido->sku,
                'stock_actual' => $pedido->cantidad,
                'stock_minimo' => $pedido->stock_minimo ?? 0,
                'gci_codigo' => $pedido->numero_seguimiento,
                
            ]);

            // Actualizar el pedido para vincular el nuevo material
            $pedido->materiales_id = $material->id;
             $pedido->save();
        }
    }

    // ============================
    // Manejo de herramientas
    // ============================
    if ($pedido->tipo === 'herramienta') {
        if ($pedido->herramientas_id) {
            // Herramienta existente: sumar stock
            $herramienta = Herramienta::find($pedido->herramientas_id);
            if ($herramienta) {
                $herramienta->cantidad += $pedido->cantidad;
                $herramienta->cantidad_disponible += $pedido->cantidad;
                $herramienta->save();
            }
        } else {
            // Herramienta nueva: crear registro
            $herramienta = Herramienta::create([
                'nombre' => $pedido->nombre,
                'codigo' => $pedido->codigo,
                'sku' => $pedido->sku,
                'cantidad' => $pedido->cantidad,
                'cantidad_disponible' => $pedido->cantidad,
                'gci_codigo' => $pedido->numero_seguimiento,
                'tipo_alimentacion' => $pedido->tipo_alimentacion,
            ]);

            // Actualizar el pedido para vincular la nueva herramienta
            $pedido->herramientas_id = $herramienta->id;
        }
    }

    // ============================
    // Marcar pedido como completado
    // ============================
    $pedido->estado = 'completado';
    $pedido->save();

    $this->dispatch('reload-page');
    session()->flash('success', 'Pedido completado y stock actualizado');
}



    // =========================
    // RENDER
    // =========================

public function render()
{
    // ===============================
    // Pedidos pendientes
    // ===============================
    $pedidosHerramientasPendientes = Pedido::where('estado', 'pendiente')
        ->where('tipo', 'herramienta')
        ->when($this->filtroNombre, fn($q) => $q->where('nombre', 'like', '%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento', 'like', '%'.$this->filtroSeguimiento.'%'))
        ->orderBy('created_at','desc')
        ->get();

    $pedidosMaterialesPendientes = Pedido::where('estado', 'pendiente')
        ->where('tipo', 'material')
        ->when($this->filtroNombre, fn($q) => $q->where('nombre', 'like', '%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento', 'like', '%'.$this->filtroSeguimiento.'%'))
        ->orderBy('created_at','desc')
        ->get();

    // Unificar pendientes en $pedidos para la tabla principal
    $pedidos = $pedidosHerramientasPendientes->merge($pedidosMaterialesPendientes);

    // ===============================
    // Historial completados
    // ===============================
    $pedidosHerramientasCompletados = Pedido::where('estado', 'completado')
        ->where('tipo', 'herramienta')
        ->when($this->filtroNombre, fn($q) => $q->where('nombre', 'like', '%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento', 'like', '%'.$this->filtroSeguimiento.'%'))
        ->orderBy('updated_at','desc')
        ->get();

    $pedidosMaterialesCompletados = Pedido::where('estado', 'completado')
        ->where('tipo', 'material')
        ->when($this->filtroNombre, fn($q) => $q->where('nombre', 'like', '%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento', 'like', '%'.$this->filtroSeguimiento.'%'))
        ->orderBy('updated_at','desc')
        ->get();

    // ===============================
    // Historial rechazados
    // ===============================
    $pedidosHerramientasRechazados = Pedido::where('estado', 'rechazado')
        ->where('tipo', 'herramienta')
        ->when($this->filtroNombre, fn($q) => $q->where('nombre', 'like', '%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento', 'like', '%'.$this->filtroSeguimiento.'%'))
        ->orderBy('updated_at','desc')
        ->get();

    $pedidosMaterialesRechazados = Pedido::where('estado', 'rechazado')
        ->where('tipo', 'material')
        ->when($this->filtroNombre, fn($q) => $q->where('nombre', 'like', '%'.$this->filtroNombre.'%'))
        ->when($this->filtroSeguimiento, fn($q) => $q->where('numero_seguimiento', 'like', '%'.$this->filtroSeguimiento.'%'))
        ->orderBy('updated_at','desc')
        ->get();

    // ===============================
    // Retornar vista
    // ===============================
    return view('livewire.panol.pedidos-index', compact(
        'pedidos', // tabla principal pendientes
        'pedidosHerramientasPendientes',
        'pedidosMaterialesPendientes',
        'pedidosHerramientasCompletados',
        'pedidosMaterialesCompletados',
        'pedidosHerramientasRechazados',
        'pedidosMaterialesRechazados'
    ))->layout('layouts.app');
}





}
