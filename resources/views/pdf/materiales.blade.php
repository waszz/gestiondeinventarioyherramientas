<h2>Listado de Materiales</h2>


<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>GCI Código</th>
            <th>Stock actual</th>
            <th>Stock mínimo</th>
            <th>Esencial</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($materiales as $material)
        <tr>
            <td>{{ $material->nombre }}</td>
            <td>{{ $material->codigo_referencia }}</td>
            <td>{{ $material->gci_codigo ?? '-' }}</td>
            <td>{{ $material->stock_actual }}</td>
            <td>{{ $material->stock_minimo }}</td>
            <td>{{ $material->material_esencial ? 'Sí' : 'No' }}</td>
            <td>
                {{ $material->stock_actual <= $material->stock_minimo ? 'Stock bajo' : 'OK' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>