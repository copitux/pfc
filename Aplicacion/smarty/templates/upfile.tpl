{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
	<h3>Subir archivo</h3>
	{if $advice.msg != ''}
	<div id="aviso">
		<h3><span class="aviso">Condiciones a tener en cuenta a la hora de subir un archivo</span></h3>
		<strong>Aviso: </strong>{$advice.msg}
		<ul>
			{section name=v loop=$advice.users}
			<li><span class="{$advice.users[v].type}">{$advice.users[v].nombre} {$advice.users[v].apellido1} {$advice.users[v].apellido2}</span></li>
			{/section}
		</ul>
	</div>
	{/if}
	<form enctype="multipart/form-data" action="" method="post">
		<fieldset>
			<input type="hidden" name="MAX_FILE_SIZE" value="{$size}" />
			<div>
				<label for="file">Archivo</label>
				<input class="upload" type="file" name="file" id="file" />
			</div>
			
			<div>
				<label for="upwrite">Sobrescribir</label>
				<input type="checkbox" name="upwrite" id="upwrite" />
			</div>
			
			<input class="boton" type="submit" name="insert" value="Subir archivo" />
			{if $fail_data != ''}<p><span class="error">{$fail_data}</span></p>{/if}
			{if $prsend != ''}<p><span class="ok">La practica "{$prsend}" se ha enviado correctamente</span></p>{/if}
		</fieldset>
		<p class="ayuda help"><span>Hasta {$size/1024/1024} <acronym title="MegaBytes">Mb</acronym></span></p>
	</form>
{include file="basic/menu.tpl"}