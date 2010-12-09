{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Archivos compartidos por <strong>{$user_share.nombre} {$user_share.apellido1} {$user_share.apellido2}</strong> </h3>
		<div id="ruta">
			<strong>Ruta: </strong> <span class="{$user_share.type}"><a href="index.php?route=teacher/dShare&id={$user_share.idUsuario}">
			{$user_share.nombre}</span></a>
			{section name=v loop=$shr_rute step=-1}
			&raquo; <a href="{$shr_rute[v].href}">{$shr_rute[v].tit}</a> 
			{/section}
			</span>
		</div>
		<table>
			<thead>
				<tr>
					<td>Nombre</td>
					<td>Opciones</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$u}
<tr>
<td class="archivo">
<span class="{$u[v].icon}"><a href="{$u[v].href}" title="{$u[v].enl}">{$u[v].enl}</a></span>
</td>
<td><span class="ap_edit">{$u[v].rn}</span> - <span class="ap_del">{$u[v].dl}</span></td>
</tr>
{/section}
			</tbody>
		</table><br />
{include file="basic/menu.tpl"}
