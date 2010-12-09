{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<h3>Crear Carpeta</h3>
	{if $advice.msg != ''}
	<div id="aviso">
		<h3><span class="aviso">Condiciones a tener en cuenta a la hora de crear esta carpeta</span></h3>
		<strong>Aviso: </strong>{$advice.msg}
		<ul>
			{section name=v loop=$advice.users}
			<li><span class="{$advice.users[v].type}">{$advice.users[v].nombre} {$advice.users[v].apellido1} {$advice.users[v].apellido2}</span></li>
			{/section}
		</ul>
	</div>
	{/if}
	<form action="" method="post">
		<fieldset>
			<input type="hidden" name="fileid" value="{$file_id}" />
			<div>
				<label for="folder">Nombre</label>
				<input maxlength="25" class="nueva_carpeta" type="text" name="folder" id="folder" />
			</div>
			
			<input class="boton" type="submit" name="insert" value="Crear" />
			{if $fail_data != ''}<p><span class="error">{$fail_data}</span></p>{/if}
		</fieldset>
		<p class="ayuda help"><span>Patrón aceptado: Carácteres alfanuméricos y ,-_</span></p>
	</form>
{include file="basic/menu.tpl"}