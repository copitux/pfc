{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Enunciados de {$asig->nombreAsignatura}</h3>
		<table>
			<thead>
				<tr>
					<td>Nombre</td>
					<td>Tama&ntilde;o</td>
					<td>Fecha</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$files}
<tr>
<td class="archivo"><span class="enunciado"><a href="{$files[v].file_enl}" title="{$files[v].file}">
{$files[v].file|truncate:40:'...'}</a></span></td>
<td>{$files[v].size|string_format:"%.2f"} Kb</td>
<td>{$files[v].date|date_format:'%d.%m.%y'}</td>
</tr>
{/section}
			</tbody>
		</table><br />
		<div class="opciones">
			<span class="practica"><a href="index.php?route=student/upFile&path={$asig->idAsignatura}">Subir practica</a></span>
		</div>
{include file="basic/menu.tpl"}
