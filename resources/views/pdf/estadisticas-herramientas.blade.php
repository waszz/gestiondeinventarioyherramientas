<h2>Estadísticas de Uso de Herramientas</h2>

<h3>Herramientas más usadas</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
<tr><th>Nombre</th><th>Código</th><th>Total</th></tr>
@foreach($stats['mas_usadas'] as $s)
<tr>
<td>{{ $s->nombre }}</td>
<td>{{ $s->codigo }}</td>
<td>{{ $s->total }}</td>
</tr>
@endforeach
</table>

<h3>Herramientas menos usadas</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
<tr><th>Nombre</th><th>Código</th><th>Total</th></tr>
@foreach($stats['menos_usadas'] as $s)
<tr>
<td>{{ $s->nombre }}</td>
<td>{{ $s->codigo }}</td>
<td>{{ $s->total }}</td>
</tr>
@endforeach
</table>

<h3>Funcionarios que más utilizan herramientas</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
<tr><th>Funcionario</th><th>Total</th></tr>
@foreach($stats['funcionarios'] as $s)
<tr>
<td>{{ $s->funcionario }}</td>
<td>{{ $s->total }}</td>
</tr>
@endforeach
</table>

<h3>Uso mensual</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
<tr><th>Mes</th><th>Total</th></tr>
@foreach($stats['uso_mensual'] as $s)
<tr>
<td>{{ $s->mes }}</td>
<td>{{ $s->total }}</td>
</tr>
@endforeach
</table>
