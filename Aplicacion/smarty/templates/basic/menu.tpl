{*
	menu.tpl	
	
	Diseño del menu de navegación de la parte izquierda
	
*}	
	</div>
	<div id="menu">
		{section name=v loop=$menu}
			<h3><span class="{$menu[v].class}">{$menu[v].menu}</span></h3>
			<div>
			{section name=a loop=$menu[v].submenu}
				<h4><span class="{$menu[v].submenu[a].act}"><a href="{$menu[v].submenu[a].enl}">{$menu[v].submenu[a].tit}</a></span></h4>
			{/section}				
			</div>
		{/section}
	</div>
