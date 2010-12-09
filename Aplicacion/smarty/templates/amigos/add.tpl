{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<h3>A&ntilde;adir amigo</h3>
	<div id="aviso">
		<h3><span class="aviso">
		Para a&ntilde;adir a un amigo inserte su <acronym title="Identificativo de usuario; por ejemplo el suyo es '{$user.login}'">login id</acronym>. Si desea 
		a&ntilde;adir mas de un amigo basta con separarlos por espacios.
		</span></h3>

	</div>
	<form action="" method="post">
		<fieldset>
			<div>
				<label for="amigos">Nombre</label>
				<input maxlength="50" class="add_user" type="text" name="amigos" id="amigos" />
			</div>
			
			<input class="boton" type="submit" name="insert" value="A&ntilde;adir amigos" />
			{if $fail_data != ''}<p><span class="error">{$fail_data}</span></p>{/if}
		</fieldset>
		<p class="ayuda help"><span>Patrón aceptado: Alfanuméricos</span></p>
	</form>	
{include file="basic/menu.tpl"}