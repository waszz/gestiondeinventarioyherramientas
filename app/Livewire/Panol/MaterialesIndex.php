<?php

namespace App\Livewire\Panol;

use App\Imports\MaterialesImport;
use App\Models\Funcionario;
use App\Models\Material;
use App\Models\MovimientoMaterial;
use App\Models\Pedido;
use App\Models\TipoMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;



class MaterialesIndex extends Component
{
    use WithFileUploads; 
    public $buscar = '';

    public $materialAEliminar = null;
    public $nombreMaterial = '';
    public $mostrarModalRetiro = false;
    public $mostrarModalIngreso = false;
    public $stockIngreso;
    public $stockMinimoIngreso;
    public $filtroDesde;
    public $filtroHasta;
    public $filtroTipo;
    public $filtroBusqueda;
    public $filtroDestino;
    public $filtroTicket;
    public $filtroFuncionario;
    public $mostrarModalEditar = false;
    public $materialEditarId = null;
    public $nombreEditar = '';
    public $codigoEditar = '';

    public $materialSeleccionado = null;
    public $cantidadRetiro;
    public $destinoRetiro;
    public $ticketRetiro;
    public $funcionario_id;
    public $funcionarios = [];
    public bool $historialAbierto = false;
    public $mostrarEstadisticas = false;
    public $filtroEstDesde = '';
    public $filtroEstHasta = '';
    public $filtroEstBusqueda = '';
    public $mostrarModalEliminarMaterial = false;
    public $mostrarModalPedido = false;
    public $materialPedidoId;
    public $cantidadPedido = 1;
    public $skuPedido;
    public $materialTienePedidoPendiente = false;
    public $hayStockBajo = false;
    public $archivoImportacion;
    public $tiposMateriales = ['Eléctrico','Sanitario','Herrería','Construcción','Servicios','Otros', 'Herramientas'];
    public $columnasDetectadas = [];
    public $columnaNombre;
    public $columnaGci;
    public $columnaStock;
    public $columnaStockMinimo;
    public $mostrarConfiguracion = false;
    public $filtroEstado; // 'critico' o 'optimo' o null
    public $seleccionados = [];
    public $seleccionarTodos = false;
    protected $listeners = ['refreshMaterial' => '$refresh'];

public function updatedSeleccionados()
{
    $total = Material::count();

    $this->seleccionarTodos = count($this->seleccionados) === $total;
}

    public function updatedSeleccionarTodos($valor)
{
    if ($valor) {
        $this->seleccionados = Material::pluck('id')->toArray();
    } else {
        $this->seleccionados = [];
    }
}

public function eliminarSeleccionados()
{
    if (empty($this->seleccionados)) {
        session()->flash('error', 'No hay materiales seleccionados.');
        return;
    }

    // Eliminar movimientos relacionados
    MovimientoMaterial::whereIn('material_id', $this->seleccionados)->delete();

    // Eliminar materiales
    Material::whereIn('id', $this->seleccionados)->delete();

    $this->seleccionados = [];
    $this->seleccionarTodos = false;

    session()->flash('success', 'Materiales eliminados correctamente.');
    $this->dispatch('reload-page');
}

public function cambiarTipoMaterial($materialId, $nuevoTipo)
{
    $material = Material::find($materialId);
    if (!$material) return;

    // Buscar o crear el tipo
    $tipo = TipoMaterial::firstOrCreate(['nombre' => $nuevoTipo]);

    // Actualizar el material
    $material->tipo_material_id = $tipo->id;
    $material->save();

    // Recargar la relación para Livewire
    $material->load('tipo');
    
    session()->flash('success', 'Tipo de material actualizado correctamente.');
}

private function detectarTipo($nombreMaterial)
{
    $nombre = strtolower($nombreMaterial);

    if (str_contains($nombre, 'cable') || str_contains($nombre, 'enchufe') || str_contains($nombre, 'llave termica')) {
        return TipoMaterial::firstOrCreate(['nombre' => 'Eléctrico']);
    }

    if (str_contains($nombre, 'caño') || str_contains($nombre, 'valvula') || str_contains($nombre, 'griferia')) {
        return TipoMaterial::firstOrCreate(['nombre' => 'Sanitario']);
    }

    if (str_contains($nombre, 'arena') || str_contains($nombre, 'cemento')) {
        return TipoMaterial::firstOrCreate(['nombre' => 'Construcción']);
    }

    return null; // Si no se detecta
}
    
    /*
    |---------------------------------------------------
    | IMPORTAR COMO CSV, XLSX
    |---------------------------------------------------
    */

    public function updatedArchivoImportacion()
{
    $data = Excel::toArray([], $this->archivoImportacion);

    if (!isset($data[0][0])) return;

    $this->columnasDetectadas = array_keys($data[0][0]);
}



public function importarMateriales()
{
    $this->resetErrorBag();

    $columnas = [
        'columnaNombre'      => $this->columnaNombre,
        'columnaGci'         => $this->columnaGci,
        'columnaStock'       => $this->columnaStock,
        'columnaStockMinimo' => $this->columnaStockMinimo,
    ];

    // 1. Validaciones de archivos y campos requeridos
    $this->validate([
        'archivoImportacion' => 'required|file|mimes:csv,xlsx',
        'columnaNombre'      => 'required',
    ]);

    // 2. Lógica de duplicados (permitiendo el índice 0)
    $filtrados = array_filter($columnas, function($valor) {
        return $valor !== null && $valor !== '';
    });

    if (count($filtrados) !== count(array_unique($filtrados))) {
        //  @error('duplicado') 
        $this->addError('duplicado', 'No puedes seleccionar la misma columna para campos diferentes.');
        return;
    }

    // 3. Proceso de Importación
    try {
        Excel::import(
            new MaterialesImport(
                $this->columnaNombre,
                $this->columnaGci,
                $this->columnaStock,
                $this->columnaStockMinimo
            ),
            $this->archivoImportacion
        );

        $this->reset(['archivoImportacion', 'columnaNombre', 'columnaGci', 'columnaStock', 'columnaStockMinimo', 'columnasDetectadas', 'mostrarConfiguracion']);
        session()->flash('success', 'Materiales importados correctamente.');
        $this->dispatch('reload-page');

    } catch (\Exception $e) {
        $this->addError('archivoImportacion', 'Error: ' . $e->getMessage());
    }
}

    /*
    |---------------------------------------------------
    | EXPORTAR COMO PDF
    |---------------------------------------------------
    */

    public function exportarMaterialesPdf()
{
    $materiales = Material::query()
        ->when($this->buscar, fn($q) =>
            $q->where('nombre', 'like', '%'.$this->buscar.'%')
              ->orWhere('codigo_referencia', 'like', '%'.$this->buscar.'%')
        )
        ->orderBy('nombre')
        ->get();

    $pdf = Pdf::loadView('pdf.materiales', compact('materiales'))
        ->setPaper('A4', 'landscape');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "materiales_" . now()->format('Ymd_His') . ".pdf"
    );
}

    public function exportarEstadisticasPdf()
{
    $estadisticas = $this->estadisticasMasConsumidos;

    $pdf = Pdf::loadView('pdf.estadisticas-materiales', compact('estadisticas'))
        ->setPaper('A4', 'portrait');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "estadisticas_materiales_" . now()->format('Ymd_His') . ".pdf"
    );
}

    public function exportarHistorialPdf()
{
    $movimientos = MovimientoMaterial::with('material','funcionario')
        ->orderBy('created_at','desc')
        ->get();

    $pdf = Pdf::loadView('pdf.historial-materiales', compact('movimientos'))
        ->setPaper('A4', 'landscape');

    return response()->streamDownload(
        fn () => print($pdf->output()),
        "historial_materiales_" . now()->format('Ymd_His') . ".pdf"
    );
}


      /*
    |---------------------------------------------------
    | EXPORTAR COMO CSV
    |---------------------------------------------------
    */

    public function exportarEstadisticasCsv()
{
    $estadisticas = $this->estadisticasMasConsumidos;

    $filename = "estadisticas_materiales_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate",
        "Expires"             => "0"
    ];

    $callback = function() use ($estadisticas) {

        $file = fopen('php://output', 'w');

        // UTF8 BOM
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Encabezados
        fputcsv($file, [
            'Material',
            'Código',
            'Total consumido'
        ], ';');

        foreach ($estadisticas as $item) {
            fputcsv($file, [
                $item->material->nombre ?? '',
                $item->material->codigo_referencia ?? '',
                $item->total_consumido
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function exportarHistorialCsv()
{
    $movimientos = MovimientoMaterial::with('material','funcionario')
        ->orderBy('created_at','desc')
        ->get();

    $filename = "historial_materiales_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate",
        "Expires"             => "0"
    ];

    $callback = function() use ($movimientos) {

        $file = fopen('php://output', 'w');

        // BOM UTF-8 para Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // ✅ Encabezados
        fputcsv($file, [
            'Fecha',
            'Material',
            'Código',
            'GCI Código',
            'Tipo',
            'Cantidad',
            'Destino',
            'Funcionario',
            'Ticket',
            'Usuario'
        ], ';');

        foreach ($movimientos as $mov) {
            fputcsv($file, [
                $mov->created_at->format('d/m/Y H:i'),
                $mov->material->nombre ?? '',
                $mov->material->codigo_referencia ?? '',
                $mov->material->gci_codigo ?? '',
                $mov->tipo,
                $mov->cantidad,
                $mov->destino,
                trim(
                    (optional($mov->funcionario)->nombre ?? '') . ' ' .
                    (optional($mov->funcionario)->apellido ?? '')
                ),
                $mov->ticket,
                $mov->usuario
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function exportarCsv()
{
    $materiales = Material::orderBy('nombre')->get();

    $filename = "materiales_" . now()->format('Ymd_His') . ".csv";

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($materiales) {

        $file = fopen('php://output', 'w');

        // BOM UTF-8 para Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // ✅ Encabezados
        fputcsv($file, [
            'Nombre',
            'Código',
            'GCI Código', 
            'Stock Actual',
            'Stock Mínimo',
            'Esencial',
            'Estado'
        ], ';');

        // ✅ Filas
        foreach ($materiales as $material) {
            fputcsv($file, [
                $material->nombre,
                $material->codigo_referencia,
                $material->gci_codigo ?? '', 
                $material->stock_actual,
                $material->stock_minimo,
                $material->material_esencial ? 'Sí' : 'No',
                $material->stock_actual <= $material->stock_minimo ? 'Stock bajo' : 'OK'
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function abrirModalPedido($materialId)
{
    $this->materialPedidoId = $materialId;
    $this->cantidadPedido = 1;
    $this->skuPedido = null;

    $this->mostrarModalPedido = true;
}
public function abrirModalPedidoMaterial($materialId)
{
    $this->materialSeleccionado = Material::find($materialId);

    $this->materialTienePedidoPendiente = Pedido::where('materiales_id', $materialId)
        ->where('estado', 'pendiente')
        ->exists();

    $this->mostrarModalPedido = true;
}

public function guardarPedidoMaterial()
{
    $this->validate([
        'cantidadPedido' => 'required|integer|min:1'
    ]);

    $material = Material::find($this->materialPedidoId);
    if (!$material) return;

    Pedido::create([
        'tipo' => 'material',
        'materiales_id' => $material->id,
        'nombre' => $material->nombre,
        'codigo' => $material->codigo_referencia,
        'cantidad' => $this->cantidadPedido,
        'estado' => 'pendiente',
        'sku' => $this->skuPedido,
        'numero_seguimiento' => $material->gci_codigo ?? 'N/A', // <-- acá usamos el GCI
    ]);

    $this->materialTienePedidoPendiente = true;
    $this->mostrarModalPedido = false;

    $this->dispatch('reload-page');
    session()->flash('success', 'Pedido generado correctamente');
}

    /*
    |---------------------------------------------------
    | CONFIRMAR ELIMINACION
    |---------------------------------------------------
    */
   
    public function abrirModalEliminarMaterial($materialId)
    {
        $this->materialSeleccionado = Material::find($materialId);
        $this->mostrarModalEliminarMaterial = true;
    }

    /*
    |---------------------------------------------------
    | CANCELAR ELIMINACION
    |---------------------------------------------------
    */
    
    public function confirmarEliminarMaterial()
    {
        $this->materialSeleccionado->delete();
        $this->mostrarModalEliminarMaterial = false;
        session()->flash('success', 'Material eliminado correctamente');
        $this->dispatch('reload-page');
    }

    /*
    |---------------------------------------------------
    | ELIMINAR MATERIAL
    |---------------------------------------------------
    */
    public function eliminarMaterial()
    {
        if (!$this->materialAEliminar) return;

        MovimientoMaterial::where('material_id', $this->materialAEliminar)->delete();
        Material::destroy($this->materialAEliminar);

        $this->cancelarEliminacion();
        session()->flash('success', 'Material eliminado correctamente');
        $this->dispatch('reload-page');
    }

     /*
    |---------------------------------------------------
    |RETIRAR MATERIAL
    |---------------------------------------------------
    */

    public function abrirModalRetiro($materialId)
{
    $this->materialSeleccionado = Material::find($materialId);

    $this->funcionarios = Funcionario::orderBy('nombre')->get();

    $this->cantidadRetiro = '';
    $this->destinoRetiro = '';
    $this->ticketRetiro = '';
    $this->funcionario_id = '';

    $this->mostrarModalRetiro = true;
}

  /*
    |---------------------------------------------------
    |EDITAR NOMBRE MATERIAL
    |---------------------------------------------------
    */

public function editarMaterial($id)
{
    $material = Material::findOrFail($id);

    $this->materialEditarId = $material->id;
    $this->nombreEditar = $material->nombre;
    $this->codigoEditar = $material->codigo_referencia;

    $this->mostrarModalEditar = true;
}
 /*
    |---------------------------------------------------
    |GUARDAR NOMBRE MATERIAL
    |---------------------------------------------------
    */

public function guardarEdicionMaterial()
{
    $this->validate([
        'nombreEditar' => 'required|string|max:255',
        'codigoEditar' => 'required|string|max:255',
    ]);

    $material = Material::findOrFail($this->materialEditarId);

    $material->update([
        'nombre' => $this->nombreEditar,
        'codigo_referencia' => $this->codigoEditar,
    ]);
    
    $this->mostrarModalEditar = false;
    session()->flash('success', 'Material actualizado correctamente');
    $this->dispatch('reload-page');
}

  /*
    |---------------------------------------------------
    |GUARDAR RETIRO
    |---------------------------------------------------
    */

public function guardarRetiro()
{
    $this->validate([
        'cantidadRetiro' => 'required|numeric|min:1',
        'destinoRetiro' => 'required|string|max:255',
        'ticketRetiro' => 'required|string|max:100',
        'funcionario_id' => 'required|exists:funcionarios,id',
    ]);

    $material = Material::find($this->materialSeleccionado->id);

    if (!$material) {
        session()->flash('error', 'Material no encontrado');
        return;
    }

    // evitar negativo
    if ($material->stock_actual <= 0) {
        session()->flash('error', 'Sin stock disponible');
        return;
    }

    if ($material->stock_actual < $this->cantidadRetiro) {
        session()->flash('error', 'No hay stock suficiente.');
        return;
    }

    // descontar stock
    $material->stock_actual -= $this->cantidadRetiro;
    $material->save();

    // registrar movimiento
  MovimientoMaterial::create([
    'material_id' => $material->id,
    'tipo' => 'salida',
    'cantidad' => $this->cantidadRetiro,
    'motivo' => 'Retiro para funcionario',
    'usuario' => auth()->user()->name ?? 'Sistema',
    'destino' => $this->destinoRetiro,
    'ticket' => $this->ticketRetiro,
    'funcionario_id' => $this->funcionario_id,
]);

    // cerrar modal
    $this->mostrarModalRetiro = false;

    // limpiar campos
    $this->reset([
        'cantidadRetiro',
        'destinoRetiro',
        'ticketRetiro',
        'funcionario_id'
    ]);

    session()->flash('success', 'Material retirado correctamente.');
    $this->dispatch('reload-page');
   
}



public function abrirModalIngreso($materialId)
{
    $this->materialSeleccionado = Material::find($materialId);

    $this->stockIngreso = $this->materialSeleccionado->stock_actual;
    $this->stockMinimoIngreso = $this->materialSeleccionado->stock_minimo;

    $this->mostrarModalIngreso = true;
}

public function guardarIngreso()
{ 
    $this->validate([
        'stockIngreso' => 'required|numeric|min:0',
        'stockMinimoIngreso' => 'required|numeric|min:0',
    ]);

    $material = $this->materialSeleccionado;

    // Calcular la cantidad ingresada (para el historial)
    $cantidadIngresada = $this->stockIngreso - $material->stock_actual;

    // Actualizar stock y stock mínimo
    $material->stock_actual = $this->stockIngreso;
    $material->stock_minimo = $this->stockMinimoIngreso;
    $material->save();

    // Registrar en movimientos_material solo si hubo cambio de stock
    if ($cantidadIngresada > 0) {
        MovimientoMaterial::create([
            'material_id' => $material->id,
            'tipo' => 'entrada', // ingreso
            'cantidad' => $cantidadIngresada,
            'motivo' => 'Ingreso de stock',
            'usuario' => auth()->user()->name ?? 'Sistema',
            'destino' => null,
            'ticket' => null,
            'funcionario_id' => null,
        ]);
    }

    $this->reset(['mostrarModalIngreso', 'stockIngreso', 'stockMinimoIngreso']);
    session()->flash('success', 'Stock actualizado correctamente.');
    $this->dispatch('reload-page');
}
    /*
    |---------------------------------------------------
    | STOCK MINIMO
    |---------------------------------------------------
    */
    public function actualizarStockMinimo($id, $valor)
    {
        $material = Material::find($id);
        if (!$material) return;

        $material->stock_minimo = (int) $valor;
        $material->save();
    }

    /*
    |---------------------------------------------------
    | AJUSTAR STOCK
    |---------------------------------------------------
    */
    public function ajustarStock($id, $nuevoStock)
    {
        $material = Material::find($id);
        if (!$material) return;

        $nuevoStock = (int) $nuevoStock;

        $diferencia = $nuevoStock - $material->stock_actual;
        if ($diferencia == 0) return;

        MovimientoMaterial::create([
            'material_id' => $material->id,
            'tipo' => 'ajuste',
            'cantidad' => abs($diferencia),
            'motivo' => 'Ajuste manual',
            'usuario' => auth()->user()->name ?? 'Sistema',
        ]);

        $material->stock_actual = $nuevoStock;
        $material->save();
        $this->dispatch('$refresh');
    }

    /*
|---------------------------------------------------
| TOGGLE MATERIAL ESENCIAL
|---------------------------------------------------
*/
public function toggleEsencial($id)
{
    $material = Material::find($id);
    if (!$material) return;

    $material->material_esencial = !$material->material_esencial;
    $material->save();
}

    /*
|---------------------------------------------------
| TOGGLE HISTORIAL
|---------------------------------------------------
*/
public function toggleHistorial()
{
    $this->historialAbierto = !$this->historialAbierto;
}

    /*
|---------------------------------------------------
| TOGGLE HISTORIAL DE MATERIALES CONSUMIDOS
|---------------------------------------------------
*/

public function getEstadisticasMasConsumidosProperty()
{
    $query = MovimientoMaterial::selectRaw('material_id, SUM(cantidad) as total_consumido')
        ->where('tipo', 'salida'); // egresos

    if ($this->filtroEstDesde) {
        $query->where('created_at', '>=', \Carbon\Carbon::parse($this->filtroEstDesde)->startOfDay());
    }

    if ($this->filtroEstHasta) {
        $query->where('created_at', '<=', \Carbon\Carbon::parse($this->filtroEstHasta)->endOfDay());
    }

    if ($this->filtroEstBusqueda) {
        $query->whereHas('material', fn($q) =>
            $q->where('nombre', 'like', '%'.$this->filtroEstBusqueda.'%')
              ->orWhere('codigo_referencia', 'like', '%'.$this->filtroEstBusqueda.'%')
        );
    }

    $result = $query->groupBy('material_id')
        ->with('material')
        ->orderByDesc('total_consumido')
        ->get();

    return $result;
}


    /*
    |---------------------------------------------------
    | RENDER
    |---------------------------------------------------
    */
public function render()
{


    //Tabla de materiales
$materiales = Material::query()
    ->when($this->buscar, function($q) {
        $q->where(function($q2) {
            $q2->where('nombre', 'like', '%'.$this->buscar.'%')
               ->orWhere('codigo_referencia', 'like', '%'.$this->buscar.'%')
               ->orWhereHas('tipo', function($q3) {
                   $q3->where('nombre', 'like', '%'.$this->buscar.'%');
               });
        });
    })
    ->when($this->filtroEstado === 'critico', fn($q) =>
        $q->whereColumn('stock_actual', '<=', 'stock_minimo')
    )
    ->when($this->filtroEstado === 'optimo', fn($q) =>
        $q->whereColumn('stock_actual', '>', 'stock_minimo')
    )
    ->orderBy('nombre')
    ->get();
        $this->hayStockBajo = Material::whereColumn('stock_actual', '<=', 'stock_minimo')->exists();

    // Movimientos filtrados
    $movimientos = MovimientoMaterial::with('material', 'funcionario')
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
            $q->whereHas('material', fn($q2) =>
                $q2->where('nombre', 'like', '%'.$this->filtroBusqueda.'%')
                   ->orWhere('codigo_referencia', 'like', '%'.$this->filtroBusqueda.'%')
            )
        )
        ->when($this->filtroDestino, fn($q) =>
            $q->where('destino', 'like', '%'.$this->filtroDestino.'%')
        )
        ->when($this->filtroTicket, fn($q) =>
            $q->where('ticket', 'like', '%'.$this->filtroTicket.'%')
        )
        ->when($this->filtroFuncionario, fn($q) =>
            $q->whereHas('funcionario', fn($qf) =>
                $qf->where('nombre', 'like', '%'.$this->filtroFuncionario.'%')
                   ->orWhere('apellido', 'like', '%'.$this->filtroFuncionario.'%')
            )
        )
        ->orderBy('created_at', 'desc')
        ->get();

    //Creaciones filtradas solo si tipo es vacío o 'creacion', y no hay filtro de ticket/destino/funcionario
    $creaciones = collect();
    if ((!$this->filtroTipo || $this->filtroTipo === 'creacion')
        && !$this->filtroDestino
        && !$this->filtroTicket
        && !$this->filtroFuncionario
    ) {
        $creaciones = Material::query()
            ->when($this->filtroDesde, fn($q) =>
                $q->whereDate('created_at', '>=', \Carbon\Carbon::parse($this->filtroDesde)->startOfDay())
            )
            ->when($this->filtroHasta, fn($q) =>
                $q->whereDate('created_at', '<=', \Carbon\Carbon::parse($this->filtroHasta)->endOfDay())
            )
            ->when($this->filtroBusqueda, fn($q) =>
                $q->where('nombre', 'like', '%'.$this->filtroBusqueda.'%')
                  ->orWhere('codigo_referencia', 'like', '%'.$this->filtroBusqueda.'%')
            )
            ->get()
            ->map(fn($m) => (object)[
                'created_at' => $m->created_at,
                'material' => $m,
                'tipo' => 'creacion',
                'cantidad' => $m->stock_actual,
                'motivo' => 'Creación del material',
                'destino' => null,
                'ticket' => null,
                'funcionario' => null,
            ]);
    }

    //Combinar movimientos y creaciones
    $historial = $movimientos->concat($creaciones)
        ->sortByDesc('created_at');

    return view('livewire.panol.materiales-index', [
        'materiales' => $materiales,
        'movimientos' => $historial,
    ])->layout('layouts.app');
}



}
