<?php


use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\DonacionController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FoooterController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SobreMiController;
use App\Http\Controllers\TodosLosPostsController;
use App\Livewire\CrearCompra;
use App\Livewire\CrearLicencia;
use App\Livewire\EditarCompra;
use App\Livewire\EditarLicencia;
use App\Livewire\EditarPlanilla;
use App\Livewire\FuncionarioEdit;
use App\Livewire\Funcionarios;
use App\Livewire\GenerarPlanilla;
use App\Livewire\Licencias;
use App\Livewire\Panol\HerramientaCreate;
use App\Livewire\Panol\HerramientasIndex;
use App\Livewire\Panol\MaterialCreate;
use App\Livewire\Panol\MaterialesIndex;
use App\Livewire\Panol\PedidosIndex;
use App\Livewire\PublicPosts;
use App\Livewire\ReportesIndex;
use App\Livewire\VerCompras;
use App\Models\Planilla;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;;
use Illuminate\Support\Facades\Route;


Route::get('/', HomeController::class)->name('home');

Route::get('/dashboard', [PostController::class, 'index'] )->middleware(['auth', 'verified'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'] )->middleware(['auth', 'verified'])->name('posts.create');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'] )->middleware(['auth', 'verified'])->name('posts.edit');
Route::get('/posts/{post}', [PostController::class, 'show'] )->name('posts.show');
Route::get('/footer', [FoooterController::class, 'index'])->name('footer.index');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//EDITAR
Route::middleware(['auth'])->group(function() {
    Route::get('/planillas/{id}/editar', EditarPlanilla::class)
         ->name('planillas.editar');
});

// Descargar PDF
Route::get('/planillas/{planilla}/pdf', function(Planilla $planilla) {
    $pdf = Pdf::loadView('planillas.imprimir', compact('planilla'))->setPaper('a4');
    return $pdf->download('planilla-' . $planilla->id . '.pdf');
})->name('planillas.pdf');

// Abrir PDF en navegador para imprimir
Route::get('/planillas/{planilla}/imprimir', function(Planilla $planilla) {
    $pdf = Pdf::loadView('planillas.imprimir', compact('planilla'))->setPaper('a4');
    return $pdf->stream('planilla-' . $planilla->id . '.pdf'); // ⚡ usar stream en vez de download
})->name('planillas.imprimir');
// Descargar PDF filtrado
Route::get('/planillas/pdf-filtradas', function(Request $request) {
    $query = Planilla::query();

    if ($request->empresa) {
        $query->where('empresa', $request->empresa);
    }

    if ($request->fecha_desde && $request->fecha_hasta) {
        $desde = Carbon::parse($request->fecha_desde)->startOfDay();
        $hasta = Carbon::parse($request->fecha_hasta)->endOfDay();

        $query->where(function ($q) use ($desde, $hasta) {
            $q->whereBetween('fecha_inicio', [$desde, $hasta])
              ->orWhereBetween('fecha_fin', [$desde, $hasta])
              ->orWhere(function ($q2) use ($desde, $hasta) {
                  $q2->where('fecha_inicio', '<=', $desde)
                     ->where('fecha_fin', '>=', $hasta);
              });
        });
    }

    //solo planillas autorizadas
    $query->where('estado_autorizacion', 'autorizado');

    $planillas = $query->get();

    $pdf = Pdf::loadView('planillas.pdf_filtradas', [
        'planillas'   => $planillas,
        'empresa'     => $request->empresa,
        'fecha_desde' => $request->fecha_desde,
        'fecha_hasta' => $request->fecha_hasta
    ])->setPaper('a4', 'landscape');

    return $pdf->download('planilla_filtrada.pdf');
})->name('planillas.pdfFiltradas');


// Abrir PDF filtrado en navegador para imprimir
Route::get('/planillas/pdf-filtradas/abrir', function(Request $request) {
    $query = Planilla::query();

    if ($request->empresa) {
        $query->where('empresa', $request->empresa);
    }

    if ($request->fecha_desde && $request->fecha_hasta) {
        $desde = Carbon::parse($request->fecha_desde)->startOfDay();
        $hasta = Carbon::parse($request->fecha_hasta)->endOfDay();

        $query->where(function ($q) use ($desde, $hasta) {
            $q->whereBetween('fecha_inicio', [$desde, $hasta])
              ->orWhereBetween('fecha_fin', [$desde, $hasta])
              ->orWhere(function ($q2) use ($desde, $hasta) {
                  $q2->where('fecha_inicio', '<=', $desde)
                     ->where('fecha_fin', '>=', $hasta);
              });
        });
    }

    //solo planillas autorizadas
    $query->where('estado_autorizacion', 'autorizado');

    $planillas = $query->get();

    $pdf = Pdf::loadView('planillas.pdf_filtradas', [
        'planillas'   => $planillas,
        'empresa'     => $request->empresa,
        'fecha_desde' => $request->fecha_desde,
        'fecha_hasta' => $request->fecha_hasta
    ])->setPaper('a4', 'landscape');

    return $pdf->stream('planilla_filtrada.pdf');
})->name('planillas.imprimirFiltradas');

//GENERAR PLANILLA 
Route::get('/generar-planilla', GenerarPlanilla::class)->name('planillas.generar');
require __DIR__.'/auth.php';

//FUNCIONARIOS
Route::get('/funcionarios', Funcionarios::class)->name('funcionarios.index');
Route::get('/funcionarios/{id}/editar', FuncionarioEdit::class)->name('funcionario.edit');


//COMPRAS 
Route::middleware(['auth'])->group(function () {
    Route::get('/compras/crear', CrearCompra::class)->name('compras.create');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/compras', VerCompras::class)->name('compras.index');
});

Route::get('/compras/{compra}/editar', EditarCompra::class)->name('compras.editar');

//LICENCIAS
Route::middleware(['auth'])->group(function () {
    Route::get('/licencias', Licencias::class)->name('licencias.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/licencias/crear', CrearLicencia::class)->name('licencias.create');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/licencias/{id}/editar', EditarLicencia::class)->name('licencias.editar');

});

//INVENTARIO

Route::get('/panol/materiales', MaterialesIndex::class)
    ->name('panol.materiales');
   

Route::get('/panol/materiales/crear', MaterialCreate::class)
    ->name('panol.materiales.crear');


  Route::get('/panol/herramientas', HerramientasIndex::class)
    ->name('panol.herramientas');
   

Route::get('/panol/herramienta/crear', HerramientaCreate::class)
    ->name('panol.herramientas.crear');

    
  Route::get('/panol/pedidos', PedidosIndex::class)
    ->name('panol.pedidos');


   Route::get('/reportes', ReportesIndex::class)->name('reportes.index');

   // Mostrar PDF en navegador
Route::get('/reportes/pdf/{tipo}', [ReporteController::class, 'mostrarPDF'])
    ->name('reportes.pdf');

Route::get('/exportar-materiales-pdf', [ExportController::class, 'materialesPdf'])
    ->name('materiales.exportar.pdf');


Route::get('/historial/exportar', [HistorialController::class, 'exportar'])
    ->name('historial.exportar');