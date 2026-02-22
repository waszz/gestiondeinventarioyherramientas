<h2>Historial de Movimientos</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Material</th>
            <th>Código</th>
            <th>GCI Código</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Destino</th>
            <th>Funcionario</th>
            <th>Ticket</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movimientos as $mov)
        <tr>
            <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $mov->material->nombre ?? '' }}</td>
            <td>{{ $mov->material->codigo_referencia ?? '' }}</td>
            <td>{{ $mov->material->gci_codigo ?? '-' }}</td>
            <td>{{ $mov->tipo }}</td>
            <td>{{ $mov->cantidad }}</td>
            <td>{{ $mov->destino }}</td>
            <td>
                {{ optional($mov->funcionario)->nombre }}
                {{ optional($mov->funcionario)->apellido }}
            </td>
            <td>{{ $mov->ticket }}</td>
            <td>{{ $mov->usuario }}</td>
        </tr>
        @endforeach
    </tbody>
</table>