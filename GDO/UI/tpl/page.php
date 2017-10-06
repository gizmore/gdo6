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
    <? # Website::displayMeta(); ?>
    <?= Website::displayLink(); ?>
    <link href="GDO/Core/thm/default/css/gdo6.css" rel="stylesheet" />
    <link href="GDO/Core/thm/default/css/gdo6-classic.css" rel="stylesheet" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow" />
  </head>
  <body>
    <header><?= GDT_Bar::make()->horizontal()->yieldHook('TopBar'); ?></header>
	<div class="gdo-body">
	  <nav id="gdo-left-bar"><?= GDT_Bar::make()->vertical()->yieldHook('LeftBar'); ?></nav>
	  <main><?= $page->html; ?></main>
	  <nav id="gdo-right-bar"><?= GDT_Bar::make()->vertical()->yieldHook('RightBar'); ?></nav>
	</div>
    <footer><?= GDT_Bar::make()->horizontal()->yieldHook('BottomBar'); ?></footer>
    <?= Javascript::displayJavascripts(Module_Core::instance()->cfgMinifyJS() === 'concat'); ?>
  </body>
</html>
