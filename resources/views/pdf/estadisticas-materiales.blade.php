<h2>Estadísticas de Consumo de Materiales</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Material</th>
            <th>Código</th>
            <th>Total consumido</th>
        </tr>
    </thead>
    <tbody>
        @foreach($estadisticas as $item)
        <tr>
            <td>{{ $item->material->nombre ?? '' }}</td>
            <td>{{ $item->material->codigo_referencia ?? '' }}</td>
            <td>{{ $item->total_consumido }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
