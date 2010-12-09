{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<form action="" method="post">
		<fieldset>
		
			<div>
				<label for="nombre">Nombre</label>
				<input class="default_icon" type="text" name="nombre" id="nombre" />
			</div>
			<div>
				<label for="apellido1">Apellido1</label>
				<input class="default_icon" type="text" name="apellido1" id="apellido1" />
			</div>
			<div>
				<label for="apellido2">Apellido2</label>
				<input class="default_icon" type="text" name="apellido2" id="apellido2" />
			</div>
			<div>
				<label for="correo">E-mail</label>
				<input class="default_icon" type="text" name="correo" id="correo" />
			</div>
			<div>
				<label for="login">Login</label>
				<input class="default_icon" type="text" name="login" id="login" />
			</div>
			<div>
				<label for="pass">Contraseña</label>
				<input class="default_icon" type="text" name="pass" id="pass" />
			</div>
			
			<input class="boton" type="submit" name="insert" value="Insertar">
		
		</fieldset>
	</form>
{include file="basic/menu.tpl"}