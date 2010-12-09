{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Enunciados de {$asig->nombreAsignatura}</h3>
		<table>
			<thead>
				<tr>
					<td>Nombre</td>
					<td>Tamaño</td>
					<td>Fecha</td>
					<td>Opciones</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$files}
<tr>
<td class="archivo"><span class="enunciado"><a href="{$files[v].file_enl}" title="{$files[v].file}">
{$files[v].file|truncate:40:'...'}</a></span></td>
<td>{$files[v].size|string_format:"%.2f"} Kb</td>
<td>{$files[v].date|date_format:'%d.%m.%y'}</td>
<td>
<span class="ap_edit"><a href="{$files[v].rename_href}">Renombrar</a></span> - 
<span class="ap_del"><a href="{$files[v].delete_href}">Eliminar</a></span>
</td>
</tr>
{/section}
			</tbody>
		</table>
		<div class="opciones">
			<span class="subir_enunciado"><a href="index.php?route=teacher/upFile&path={$asig->idAsignatura}">Subir Enunciado</a></span>
		</div>
		<br />
		<h3>Practicas de {$asig->nombreAsignatura}</h3>
		<table>
			<thead>
				<tr>
					<td>Nombre</td>
					<td>Tamaño</td>
					<td>Fecha</td>
					<td>Opciones</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$prac}
<tr>
<td class="archivo"><span class="practica"><a href="{$prac[v].file_enl}" title="{$prac[v].file}">
{$prac[v].file|truncate:40:'...'}</a></span></td>
<td>{$prac[v].size|string_format:"%.2f"} Kb</td>
<td>{$prac[v].date|date_format:'%d.%m.%y'}</td>
<td>
<span class="ap_edit"><a href="{$prac[v].rename_href}">Renombrar</a></span> - 
<span class="ap_del"><a href="{$prac[v].delete_href}">Eliminar</a></span>
</td>
</tr>
{/section}
			</tbody>
		</table>
		<br />
{include file="basic/menu.tpl"}
