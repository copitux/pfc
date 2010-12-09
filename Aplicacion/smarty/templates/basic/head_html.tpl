{*
	head_html.tpl
	
	Diseño de la cabecera básica de archivos xhtml
	
	Etiquetas abiertas sin cierre: <body><div id="contenedor">

*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">

<head>
	<title>{$az_title}</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="Geany 0.14" />
	{section name=css loop=$css_files}
		<link href="{$css_files[css]}" rel="stylesheet" type="text/css" />
	{/section}
</head>

<body>

<div id="contenedor">
