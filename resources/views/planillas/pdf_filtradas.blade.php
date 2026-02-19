<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla Generada</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1F2937;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 25px;
            margin-bottom: 15px;
        }

        .logos img {
            max-height: 80px;
        }

        h2 {
            margin: 5px 0;
            font-size: 16px;
        }

        h1 {
            margin: 5px 0 15px 0;
            font-size: 18px;
        }

        p.info {
            margin: 2px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #374151;
            padding: 6px 4px;
            text-align: left;
        }

        th {
            background-color: #E5E7EB;
            color: #111827;
        }

        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        tbody tr td {
            vertical-align: middle;
        }
        
    </style>
</head>
<body>

<div class="header">
    <div class="logos">
        <img src="{{ public_path('images/española.png') }}" alt="Logo Española">
        @if($empresa === 'Gente')
            <img src="{{ public_path('images/logo-gente.png') }}" alt="Logo Gente">
        @elseif($empresa === 'Prosepri')
            <img src="{{ public_path('images/logo-prosepri.png') }}" alt="Logo Prosepri">
        @endif
    </div>

    <h2>DPTO. DE MANTENIMIENTO - ASESP</h2>
    <h1>Planilla Generada</h1>

    <p class="info"><strong>Empresa:</strong> {{ $empresa ?? 'Todas' }}</p>
    <p class="info">
    <strong>Desde:</strong> {{ $fecha_desde ? \Carbon\Carbon::parse($fecha_desde)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : '---' }}
    &nbsp;
    <strong>Hasta:</strong> {{ $fecha_hasta ? \Carbon\Carbon::parse($fecha_hasta)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : '---' }}
</p>
</div>
<table>
    <thead>
        <tr>
            <th>N° Cobro</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Cargo</th>
            <th>Área</th>
            <th>Horario Habitual</th>
            <th>Solicita</th>
            <th>De la Fecha</th>
            <th>A la Fecha</th>
            <th>Autoriza</th> 
        </tr>
    </thead>
    <tbody>
        @foreach($planillas as $p)
            <tr>
                <td>{{ $p->numero_funcionario }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->apellido }}</td>
                <td>{{ $p->cargo }}</td>
                <td>{{ $p->area }}</td>
                <td>{{ $p->horario_habitual }}</td>
                <td>{{ $p->solicita }}</td>
                <td>{{ $p->fecha_inicio }}</td>
                <td>{{ $p->fecha_fin }}</td>
                <td>{{ $p->supervisor ? $p->supervisor->name : '---' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
