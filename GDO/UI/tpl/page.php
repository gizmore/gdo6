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
    <?# Website::displayMeta(); ?>
    <?= Website::displayLink(); ?>
    <link rel="stylesheet" href="GDO/Core/css/gdo6-classic.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow" />
  </head>
  <body>
	<div id="gdo-pagewrap">
      <header id="gdo-header"><?= GDT_Bar::make()->horizontal()->yieldHook('TopBar'); ?></header>
      <div class="gdo-body">
	    <nav id="gdo-left-bar"><?= GDT_Bar::make()->vertical()->yieldHook('LeftBar'); ?></nav>
	    <div class="gdo-main"><?= $page->html; ?></div>
	    <nav id="gdo-right-bar"><?= GDT_Bar::make()->vertical()->yieldHook('RightBar'); ?></nav>
	  </div>
	</div>
    <footer id="gdo-footer"><?= GDT_Bar::make()->horizontal()->yieldHook('BottomBar'); ?></footer>
    <?= Javascript::displayJavascripts(Module_Core::instance()->cfgMinifyJS() === 'concat'); ?>
  </body>
</html>
