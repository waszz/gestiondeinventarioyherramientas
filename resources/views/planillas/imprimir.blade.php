<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla #{{ $planilla->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-height: 80px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; width: 180px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/española.png') }}" alt="Logo">  <!-- aquí va tu logo -->
        <h2>Planilla N° {{ $planilla->id }}</h2>
    </div>

    <table>
        <tr>
            <th>Creado por</th>
            <td>{{ $planilla->user->name ?? 'Sin usuario' }}</td>
        </tr>
        <tr>
            <th>Creada el</th>
            <td>{{ \Carbon\Carbon::parse($planilla->getRawOriginal('created_at'))->locale('es')->isoFormat('D [de] MMMM [de] YYYY HH:mm') }}</td>
        </tr>
        <tr>
            <th>Horario Habitual</th>
            <td>{{ $planilla->horario_habitual }}</td>
        </tr>
        <tr>
            <th>Número de Funcionario</th>
            <td>{{ $planilla->numero_funcionario }}</td>
        </tr>
        <tr>
            <th>Nombre de Funcionario</th>
            <td>{{ $planilla->nombre }} {{ $planilla->apellido }}</td>
        </tr>
        <tr>
            <th>Registro de Faltas</th>
            <td>{{ $planilla->registro_faltas }}</td>
        </tr>
        <tr>
            <th>Solicita</th>
            <td>{{ $planilla->solicita }}</td>
        </tr>
        <tr>
            <th>De la Fecha</th>
            <td>{{ \Carbon\Carbon::parse($planilla->fecha_inicio)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>A la Fecha</th>
            <td>{{ \Carbon\Carbon::parse($planilla->fecha_fin)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Horario a realizar</th>
            <td>{{ $planilla->horario_a_realizar }}</td>
        </tr>
        <tr>
            <th>Motivos</th>
            <td>{{ $planilla->motivos }}</td>
        </tr>
        <tr>
            <th>Firma</th>
            <td>{{ $planilla->autoriza }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $planilla->estado_autorizacion ?? 'Pendiente' }}</td>
        </tr>
    </table>
</body>
</html>
