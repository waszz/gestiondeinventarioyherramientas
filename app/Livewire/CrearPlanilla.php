<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Planilla;
use App\Models\Funcionario;

class CrearPlanilla extends Component
{
    public $horario_habitual;
    public $numero_funcionario;
    public $nombre;
    public $apellido;
    public $registro_faltas;
    public $fecha_inicio; 
    public $fecha_fin;    
    public $horario_a_realizar;
    public $motivos;
    public $solicita;
    public $autoriza;
    public $empresa;
    public $cargo;
    public $area;

    public $funcionariosSugeridos = []; // Para autocomplete
    public $inputsGenerados = false; // Controla si se muestran los inputs generados
    public $mostrarSugerencias = true; // Controla si se muestra la lista de sugerencias
    public $numeroReadonly = false; // Para bloquear el input después de generar
    public $errorNumeroFuncionario = null; // Para mostrar mensaje de error


    protected $rules = [
        'empresa' => 'required|string|max:255',
        'horario_habitual' => 'nullable|string|max:255',
        'numero_funcionario' => 'required|string|max:50',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'registro_faltas' => 'nullable|string',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'horario_a_realizar' => 'nullable|string|max:255',
        'motivos' => 'nullable|string|max:255',
        'solicita' => 'required|string|max:255',
        'cargo' => 'required|string|max:255',
    ];

    // Buscar sugerencias mientras escribo
    public function updatedNumeroFuncionario($value)
    {
        if ($this->mostrarSugerencias && !$this->numeroReadonly) {
            $this->funcionariosSugeridos = Funcionario::where('numero_funcionario', 'like', "%$value%")
                ->limit(5)
                ->get();
        } else {
            $this->funcionariosSugeridos = [];
        }
    }

    // Cuando selecciono un funcionario de la lista
    public function seleccionarFuncionario($id)
    {
        $funcionario = Funcionario::find($id);

        if ($funcionario) {
            $this->numero_funcionario = $funcionario->numero_funcionario;
            $this->nombre = $funcionario->nombre;
            $this->apellido = $funcionario->apellido;
            $this->empresa = in_array($funcionario->empresa, ['Gente','Prosepri','Asoc. Española']) ? $funcionario->empresa : '';
            $this->cargo = in_array($funcionario->cargo, ['Peón','Suboficial','Oficial','Especializado']) ? $funcionario->cargo : '';
            $this->area = in_array($funcionario->area, ['Rep. Grales','Electricidad','A/A','Pintura','Administrativa']) ? $funcionario->area : '';


            $this->funcionariosSugeridos = []; // Limpiar sugerencias
            $this->inputsGenerados = true; // Mostrar inputs automáticamente
            $this->mostrarSugerencias = false; // Ocultar sugerencias
            $this->numeroReadonly = true; // Bloquear input
        }
    }

    // Generar inputs al hacer clic en el botón
  public function generarInputs()
{
    $this->errorNumeroFuncionario = null; // Resetear error

    if ($this->numero_funcionario) {
        $funcionario = Funcionario::where('numero_funcionario', $this->numero_funcionario)->first();

        if ($funcionario) {
            $this->nombre = $funcionario->nombre;
            $this->apellido = $funcionario->apellido;
            $this->empresa = in_array($funcionario->empresa, ['Gente','Prosepri','Asoc. Española']) ? $funcionario->empresa : '';
            $this->cargo = in_array($funcionario->cargo, ['Peón','Suboficial','Oficial','Especializado']) ? $funcionario->cargo : '';
            $this->area = in_array($funcionario->area, ['Rep. Grales','Electricidad','A/A','Pintura','Administrativa']) ? $funcionario->area : '';

            $this->inputsGenerados = true;
            $this->mostrarSugerencias = false;
            $this->numeroReadonly = true;
        } else {
            // Si no encuentra el funcionario
            $this->inputsGenerados = false;
             $this->mostrarSugerencias = false;
            $this->errorNumeroFuncionario = "El número de funcionario no existe.";
        }
    } else {
        $this->inputsGenerados = false;
         $this->mostrarSugerencias = false;
        $this->errorNumeroFuncionario = "Debe ingresar un número de funcionario.";
    }
}


    public function guardarPlanilla()
    {
        $this->validate();

        Planilla::create([
            'empresa' => $this->empresa,
            'horario_habitual' => $this->horario_habitual,
            'numero_funcionario' => $this->numero_funcionario,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'registro_faltas' => $this->registro_faltas,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'horario_a_realizar' => $this->horario_a_realizar,
            'motivos' => $this->motivos,
            'solicita' => $this->solicita,
            'autoriza' => $this->autoriza,
            'cargo' => $this->cargo,
            'area' => $this->area,
            'user_id' => auth()->id(),
        ]);

        session()->flash('mensaje', 'La solicitud se guardó correctamente ✅');
        return redirect()->route('posts.index');
    }

    public function render()
    {
        return view('livewire.crear-planilla');
    }
}