<h2>Listado de Pedidos - {{ strtoupper($estado) }}</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
<thead>
<tr>
<th>Fecha</th>
<th>Tipo</th>
<th>Nombre</th>
<th>Código</th>
<th>SKU</th>
<th>Cantidad</th>
<th>Estado</th>
<th>Seguimiento</th>
<th>Observación</th>
</tr>
</thead>

<tbody>
@foreach($pedidos as $p)
<tr>
<td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
<td>{{ $p->tipo }}</td>
<td>{{ $p->nombre }}</td>
<td>{{ $p->codigo }}</td>
<td>{{ $p->sku }}</td>
<td>{{ $p->cantidad }}</td>
<td>{{ $p->estado }}</td>
<td>{{ $p->numero_seguimiento }}</td>
<td>{{ $p->observacion }}</td>
</tr>
@endforeach
</tbody>
</table>
