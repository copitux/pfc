{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
		<h3>Disco de compartidos</h3>
		<table>
			<thead>
				<tr>
					<td>Usuarios</td>
				</tr>
			</thead>
			<tbody>
{section name=v loop=$u}
<tr>
<td class="archivo"><span class="{$u[v].icon}"><a href="index.php?route=teacher/dShare&id={$u[v].idUsuario}" title="{$u[v].nombre}">
{$u[v].nombre} {$u[v].apellido1} {$u[v].apellido2}</a></span></td>
</tr>
{/section}
			</tbody>
		</table><br />
{include file="basic/menu.tpl"}