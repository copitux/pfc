{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<form action="" method="post">
		<fieldset>
			<div>
				<label for="user">Usuario</label>
				<input maxlength="25" class="datos_usuario" type="text" name="login" id="user" />
			</div>
			
			<div>
				<label for="pass">Contrase&ntilde;a</label>
				<input maxlength="25" class="key" type="password" name="pass" id="pass" />
			</div>
			
			<input class="boton" type="submit" name="insert" value="Enviar" />
			{if $notlogin != ''}<p><span class="error">{$notlogin}</span></p>{/if}
		</fieldset>
		<p class="ayuda help"><span>Recuerda que debes introducir todos los datos</span></p>
	</form>
{include file="basic/menu.tpl"}