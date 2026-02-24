<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Funcionario;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FuncionariosImport;

class Funcionarios extends Component
{
    use WithPagination, WithFileUploads;

    public $numero_funcionario, $nombre, $apellido, $cargo, $empresa, $area, $turno, $telefono;
    public $search = ''; 
    public $reloadFuncionario = 0;
    public $archivoImportacion;
    public $columnasDetectadas = []; // columnas detectadas en el Excel
    public $mostrarConfiguracion = false;

    // Columnas seleccionadas por el usuario
    public $columnaNumeroFuncionario;
    public $columnaNombre;
    public $columnaApellido;
    public $columnaCargo;
    public $columnaEmpresa;
    public $columnaArea;
    public $columnaTurno;
    public $columnaTelefono;

    protected $listeners = ['eliminarFuncionario', 'refreshFuncionario' => '$refresh'];
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'supervisor'])) {
            abort(403, 'Esta acción no está autorizada.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset([
            'numero_funcionario', 'nombre', 'apellido', 'cargo', 'empresa', 'area', 'turno', 'telefono'
        ]);
    }

    public function guardar()
    {
        $this->validate([
            'numero_funcionario' => 'required|unique:funcionarios,numero_funcionario',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'empresa' => 'required|string|max:255',
            'area' => 'required|string',
            'turno' => 'required|string',
            'telefono' => 'required|string|max:20',
        ]);

        Funcionario::create([
            'numero_funcionario' => $this->numero_funcionario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'cargo' => $this->cargo,
            'empresa' => $this->empresa,
            'area' => $this->area,
            'turno' => $this->turno,
            'telefono' => $this->telefono,
        ]);

        session()->flash('message', 'Funcionario creado.');
        $this->resetForm();
        $this->resetPage();
        $this->dispatch('funcionarioCreado');
    }

    public function eliminarFuncionario($id)
    {
        Funcionario::destroy($id);
        session()->flash('message', 'Funcionario eliminado.');
        $this->resetPage();
         $this->dispatch('reload-page');
        $this->reloadFuncionario++;
    }

    // =======================
    // IMPORTACIÓN EXCEL
    // =======================
       public function updatedArchivoImportacion()
    {
        $this->resetErrorBag();
        $this->columnasDetectadas = [];

        $this->validate([
            'archivoImportacion' => 'required|file|mimes:csv,xlsx',
        ]);

        $data = Excel::toArray([], $this->archivoImportacion);

        if (!isset($data[0][0])) return;

        $this->columnasDetectadas = array_keys($data[0][0]);
        $this->mostrarConfiguracion = true;
    }


public function importarFuncionarios()
{
    $this->resetErrorBag();

    // Validaciones básicas
    $this->validate([
        'archivoImportacion' => 'required|file|mimes:csv,xlsx',
        'columnaNumeroFuncionario' => 'required',
        'columnaNombre' => 'required',
    ]);

    $columnas = [
        'numero'   => $this->columnaNumeroFuncionario,
        'nombre'   => $this->columnaNombre,
        'apellido' => $this->columnaApellido,
        'cargo'    => $this->columnaCargo,
        'empresa'  => $this->columnaEmpresa,
        'area'     => $this->columnaArea,
        'turno'    => $this->columnaTurno,
        'telefono' => $this->columnaTelefono,
    ];

    // Validar duplicados
    $filtrados = array_filter($columnas, fn($v) => $v !== null && $v !== '');
    if (count($filtrados) !== count(array_unique($filtrados))) {
        $this->addError('duplicado', 'No puedes seleccionar la misma columna para campos diferentes.');
        return;
    }

    try {
        Excel::import(new FuncionariosImport($columnas), $this->archivoImportacion);

        $this->reset([
            'archivoImportacion',
            'columnaNumeroFuncionario',
            'columnaNombre',
            'columnaApellido',
            'columnaCargo',
            'columnaEmpresa',
            'columnaArea',
            'columnaTurno',
            'columnaTelefono',
            'columnasDetectadas',
            'mostrarConfiguracion'
        ]);

        session()->flash('success', 'Funcionarios importados correctamente.');
        $this->dispatch('reload-page');

    } catch (\Exception $e) {
        $this->addError('archivoImportacion', 'Error: ' . $e->getMessage());
    }
}

    public function render()
    {
        $query = Funcionario::query();

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_funcionario', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('cargo', 'like', "%{$search}%")
                  ->orWhere('empresa', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('turno', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $funcionarios = $query->orderBy('numero_funcionario', 'asc')->paginate(10);

        return view('livewire.funcionarios', [
            'funcionarios' => $funcionarios
        ])->layout('layouts.app');
    }
}