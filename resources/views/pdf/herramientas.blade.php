<h2>Listado de Herramientas</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>Total</th>
            <th>Disponible</th>
            <th>Préstamo</th>
            <th>Fuera servicio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($herramientas as $h)
        <tr>
            <td>{{ $h->nombre }}</td>
            <td>{{ $h->codigo }}</td>
            <td>{{ $h->cantidad }}</td>
            <td>{{ $h->cantidad_disponible }}</td>
            <td>{{ $h->cantidad_prestamo }}</td>
            <td>{{ $h->cantidad_fuera_servicio }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
