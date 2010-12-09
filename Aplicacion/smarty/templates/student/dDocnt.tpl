{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Disco docente</h3>
		<table>
			<thead>
				<tr>
					<td>Asignaturas</td>
					<td>Profesor</td>
					<td>Curso</td>
					<td>Carrera</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$asignaturas}
<tr>
<td class="archivo"><span class="asignatura"><a href="index.php?route=student/dDocnt&id={$asignaturas[v].idAsignatura}" title="{$asignaturas[v].nombreAsignatura}">
{$asignaturas[v].nombreAsignatura}</a></span></td>
<td><span class="email">{$asignaturas[v].idProfesor.nombre} {$asignaturas[v].idProfesor.apellido1} ({$asignaturas[v].idProfesor.correo})</span></td>
<td>{$asignaturas[v].cursos_idCurso.nombreCurso}</td>
<td>{$asignaturas[v].carreras_idCarrera.nombre}</td>
</tr>
{/section}
			</tbody>
		</table>{$asd}<br />
{include file="basic/menu.tpl"}