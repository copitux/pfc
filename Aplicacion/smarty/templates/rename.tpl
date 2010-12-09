{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<h3>Renombrar</h3>
	<h5><strong>Nombre original: </strong>{$file}</h5>
	<form action="" method="post">
		<fieldset>
			<div>
				<label for="file">Nombre nuevo</label>
				<input maxlength="25" class="edit" value="{$file}" type="text" name="newname" id="file" />
			</div>
			
			<input class="boton" type="submit" name="insert" value="Renombrar" />
			{if $fail_data != ''}<p><span class="error">{$fail_data}</span></p>{/if}
		</fieldset>
		<p class="ayuda help"><span>Patrón aceptado: Carácteres alfanuméricos y ,-_</span></p>
	</form>
{include file="basic/menu.tpl"}
