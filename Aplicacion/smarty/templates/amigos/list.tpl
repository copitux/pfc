{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<h3>Gestion de amigos</h3>
	<form action="" method="post">
	
	<table>
		<thead>
		<h4>Lista de amigos</h4>
		<tr>
			<td>#</td>
			<td>Nombre</td>
			<td>Apellidos</td>
		</tr>
		</thead>
		<tbody>
		{section name=v loop=$amigos}
		<tr>
		<td><input type="checkbox" name="users[]" value="{$amigos[v]->idUsuario}" /></td>
		<td><span class="amigo {$amigos[v]->userShrType()}">{$amigos[v]->nombre}</span></td>
		<td>{$amigos[v]->apellido1} {$amigos[v]->apellido2}</td>
		</tr>
		{/section}
		</tbody>
	</table>
	
	<span class="add_user"><a href="index.php?route={$user_type}/amigosAdd">A&ntilde;adir amigo</a></span>
	<span class="del_user">
	<input class="botonAmigos" type="submit" name="del_amigos" value="Eliminar amigos seleccionados" />
	</span>
	
	</form>
	{if $fail_data != ''}<p><span class="error">{$fail_data}</span></p>{/if}
	{if $del_amigos != ''}<p><span class="ok">{$del_amigos}</span></p>{/if}

{include file="basic/menu.tpl"}