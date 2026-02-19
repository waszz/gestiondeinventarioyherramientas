<h2>Historial de Herramientas</h2>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
<thead>
<tr>
<th>Fecha</th>
<th>Herramienta</th>
<th>Tipo</th>
<th>Cantidad</th>
<th>Funcionario</th>
<th>Detalle</th>
</tr>
</thead>
<tbody>
@foreach($historial as $h)
<tr>
<td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
<td>{{ $h->nombre }}</td>
<td>{{ $h->tipo }}</td>
<td>{{ $h->cantidad }}</td>
<td>{{ $h->funcionario }}</td>
<td>{{ $h->detalle }}</td>
</tr>
@endforeach
</tbody>
</table>
