<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h2>Historial de Estados</h2>

    <strong>
        {{ $funcionario->nombre }} {{ $funcionario->apellido }}
    </strong>

    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Duración</th>
            </tr>
        </thead>
        <tbody>

            @foreach($historial as $h)
                <tr>
                    <td>{{ ucfirst(str_replace('_',' ',$h->estado)) }}</td>
                    <td>{{ $h->inicio }}</td>
                    <td>{{ $h->fin ?? 'En curso' }}</td>
                    <td>
                        @if($h->fin)
                            {{ $h->inicio->diffForHumans($h->fin, true) }}
                        @else
                            Activo
                        @endif
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

</body>
</html>