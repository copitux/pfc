{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<h3>Compartir <q>{$file}</q></h3>
	<form action="" method="post">
	
	<table>
		<thead>
		<h4 class="amg">Lista de amigos</h4>
		<h5 class="amg"><span class="datos_usuario"><a href="index.php?route={$user_type}/amigos">Gestion de amigos</a></span></h5>
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
	
	<h4>Lista de permisos</h4>
	
	
		<ul class="prm">
		<li><input type="radio" name="profile" id="r1" value="1" checked="checked" /> <label for="r1">Renombrar y eliminar</label></li>
		<li><input type="radio" name="profile" id="r2" value="2" /> <label for="r2">Eliminar</label></li>
		<li><input type="radio" name="profile" id="r3" value="3" /> <label for="r3">Renombrar</label></li>
		<li><input type="radio" name="profile" id="r4" value="4" /> <label for="r4">Sin permisos</label></li>
		</ul>

	
		<input class="botonprm" type="submit" name="insert" value="Compartir" />
		{if $fail_data != ''}<p><span class="error">{$fail_data}</span></p>{/if}
	</form>
{include file="basic/menu.tpl"}