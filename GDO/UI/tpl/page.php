<?php
use GDO\Core\Website;
use GDO\Util\Javascript;
use GDO\UI\GDT_Bar;
use GDO\Core\Module_Core;
$page instanceof GDO\UI\GDT_Page;
?>
<!DOCTYPE html>
<html>
  <head>
	<?= Website::displayMeta(); ?>
	<?= Website::displayLink(); ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow" />
  </head>
  <body>
		<input type="checkbox" id="gdo-left-nav" class="gdo-nav" />
		<input type="checkbox" id="gdo-right-nav" class="gdo-nav" />

		<nav id="gdo-left-bar" class="gdo-nav-bar"><?= GDT_Bar::make()->vertical()->yieldHook('LeftBar'); ?></nav>
		<label for="gdo-left-nav"></label>

		<nav id="gdo-right-bar" class="gdo-nav-bar"><?= GDT_Bar::make()->vertical()->yieldHook('RightBar'); ?></nav>
		<label for="gdo-right-nav"></label>
  
	<div id="gdo-pagewrap">
	
	  <header id="gdo-header"><?= GDT_Bar::make()->horizontal()->yieldHook('TopBar'); ?></header>
	
	  <div class="gdo-body">
		<div class="gdo-main"><?= $page->html; ?></div>
	  </div>

	  <footer id="gdo-footer"><?= GDT_Bar::make()->horizontal()->yieldHook('BottomBar'); ?></footer>
	
	</div>
	
	<?= Javascript::displayJavascripts(Module_Core::instance()->cfgMinifyJS() === 'concat'); ?>
  </body>
</html>
