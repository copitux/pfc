{*
	head_own.tpl
	
	Diseño de la cabecera propia:
		-> div cabecera: Menu de Alumnos/Profesores/Admin
		-> div state: Indicador de Login + Espacio libre
		
	Etiquetas abiertas: <div id="cuerpo">
*}
	
	<div id="cabecera">
		<h1><a href="http://www.uemc.edu" title="Universidad Europea Miguel de Cervates"><span>UEMC</span></a></h1>
		<h2><a href="index.php" title="Proyecto: Disco duro virtual"><span><strong>Proyecto:</strong> Disco duro virtual</span></a></h2>
			{if $none != 'none'}
			<ul>
				<li><span class="alumno"><a href="index.php?route=student" title="Alumno">Alumno</a></span></li>
				<li><span class="profesor"><a href="index.php?route=teacher" title="Profesore">Profesor</a></span></li>
				<li><span class="admin"><a href="index.php?route=admin" title="Administrado">Administrador</a></span></li>
			</ul>
			{/if}
	</div> <!-- fin cabecera -->
	<div id="state">
			<!-- <span class="drive"><strong>Espacio libre: </strong>{$freeSpace|default:'?'}</span> -->
			<span class="{$user_type_css}"><strong>Login: </strong>
			{$user.nombre|default:'No conectado'} {$user.apellido1} {$user.apellido2} ({$user.login})</span>
	</div>
	<div id="cuerpo">
