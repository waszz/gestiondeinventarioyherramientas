<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte {{ $tipo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
        .titulo { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .fecha { font-size: 12px; margin-top: 15px; }
    </style>
</head>
<body>

<div class="titulo">
    @if($tipo === 'materiales')
        Reporte de materiales
    @else
        Reporte de herramientas
    @endif
</div>

<table>
    <thead>
        @if($tipo === 'materiales')
            <tr>
                <th>Material</th>
                <th>Código</th>
                <th>Stock</th>
                <th>Stock mínimo</th>
                <th>Esencial</th>
                <th>Estado</th>
            </tr>
        @else
            <tr>
                <th>Herramienta</th>
                <th>Código</th>
                <th>Disponibles</th>
                <th>En préstamo</th>
                <th>Fuera de servicio</th>
            </tr>
        @endif
    </thead>

    <tbody>
        @foreach($items as $item)
            <tr>
                @if($tipo === 'materiales')
                    <td>{{ $item->nombre }}</td>
                    <td>{{ $item->codigo_referencia }}</td>
                    <td>{{ $item->stock_actual }}</td>
                    <td>{{ $item->stock_minimo }}</td>
                    <td>{{ $item->material_esencial ? 'Sí' : 'No' }}</td>
                    <td>
                        @if($item->stock_actual <= $item->stock_minimo)
                            Stock bajo
                        @else
                            Ok
                        @endif
                    </td>
                @else
                    <td>{{ $item->nombre }}</td>
                    <td>{{ $item->codigo }}</td>
                    <td>{{ $item->cantidad_disponible }}</td>
                    <td>{{ $item->cantidad_prestamo }}</td>
                    <td>{{ $item->cantidad_fuera_servicio }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

<div class="fecha">
    Generado el: {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
