{include file="basic/head_html.tpl"}
{include file="basic/head_own.tpl"}
<strong>Nombre:</strong> {$user.nombre} <br />
<strong>Apellido1:</strong> {$user.apellido1} <br />
<strong>Apellido2:</strong> {$user.apellido2} <br />
<strong>Correo:</strong> {$user.correo} <br />
<strong>Login:</strong> {$user.login} <br />
<strong>Despacho:</strong> {$user.despacho} <br />
<strong>Ruta:</strong> 'files/{$user.login}' <br />
{include file="basic/menu.tpl"}