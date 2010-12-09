{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Disco local</h3>
		<div id="ruta">
			<strong><span class="home">Ruta: </span></strong>{$rute_err}
			{section name=v loop=$rute}
			<a href="{$rute[v].href}">{$rute[v].tit}</a> {$rute[v].sep}
			{/section}
		</div>
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
<tr class="{$files[v].check_share}">
<td class="archivo">
{if $files[v].check_share == 'share'}
<!--<p style="float: right; width:10px"><a style="display: block; width: 16px; height: 16px" href="#"><img style="border:0;" src="themes/basic/images/help.png" /></a></p>-->
	{if $files[v].mime == 'dir'}
	<span class="carpeta_compartida">
	{else}
	<span class="file_compartida">
	{/if}
{else}
<span class="{$files[v].mime}">
{/if}
<a href="{$files[v].file_enl}" title="{$files[v].file}">
{$files[v].file|truncate:30:'...'}</a></span></td>
<td>{$files[v].size|string_format:"%.2f"} <acronym title="Kilo Bytes">Kb</td>
<td>{$files[v].date|date_format:'%d.%m.%y'}</td>
<td>
<span class="ap_edit"><a href="{$files[v].rename_href}">Renombrar</a></span> - 
<span class="ap_del"><a href="{$files[v].delete_href}">Eliminar</a></span>
{if $files[v].share_mode != ''} - <span class="ap_shr"><a href="{$files[v].share_href}">{$files[v].share_mode}</a></span></td>{/if}
</tr>
{/section}
			</tbody>
		</table>
		<h3>Opciones</h3>
		<div id="opciones">
			<span class="nueva_carpeta"><a href="index.php?route=teacher/newFolder&path={$rute_to_up}">Crear Carpeta</a></span>
			<span class="upload"><a href="index.php?route=teacher/upFile&path={$rute_to_up}">Subir archivo</a></span>
			<!--<a href="#">Buscar</a>-->
		</div>
{include file="basic/menu.tpl"}