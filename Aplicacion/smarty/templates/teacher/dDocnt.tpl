{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Disco docente</h3>
		<table>
			<thead>
				<tr>
					<td>Asignaturas</td>
					<td>Alumnos</td>
					<td>Curso</td>
					<td>Carrera</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$asignaturas}
<tr>
<td class="archivo"><span class="asignatura"><a href="index.php?route=teacher/dDocnt&id={$asignaturas[v].idAsignatura}" title="{$asignaturas[v].nombreAsignatura}">
{$asignaturas[v].nombreAsignatura}</a></span></td>
<td>
	<select>
	{section name=c loop=$asignaturas[v].alumnos}
	<option>{$asignaturas[v].alumnos[c].nombre} {$asignaturas[v].alumnos[c].apellido1} {$asignaturas[v].alumnos[c].apellido2}</option>
	{/section}
	</select>
</td>
<td>{$asignaturas[v].cursos_idCurso.nombreCurso}</td>
<td>{$asignaturas[v].carreras_idCarrera.nombre}</td>
</tr>
{/section}
			</tbody>
		</table><br />
{include file="basic/menu.tpl"}