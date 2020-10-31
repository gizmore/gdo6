<?php
use GDO\Core\Website;
use GDO\Util\Javascript;
use GDO\UI\GDT_Bar;
use GDO\Core\Module_Core;
use GDO\UI\GDT_Page;
/** @var $page GDT_Page **/
?>
<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow" />
	<meta name="generator" content="GDO v<?=Module_Core::instance()->gdo_revision?>">
	<?=Website::displayMeta()?>
	<?=Website::displayLink()?>
  </head>
  <body>
		<input type="checkbox" id="gdo-left-nav" class="gdo-nav" />
		<input type="checkbox" id="gdo-right-nav" class="gdo-nav" />

		<nav id="gdo-left-bar" class="gdo-nav-bar"><?=$page->leftNav->render()?></nav>
		<label for="gdo-left-nav"></label>

		<nav id="gdo-right-bar" class="gdo-nav-bar"><?=$page->rightNav->render()?></nav>
		<label for="gdo-right-nav"></label>
  
	<div id="gdo-pagewrap">
	
	  <header id="gdo-header"><?=$page->topNav->render()?></header>
	
	  <div class="gdo-body">
		<div class="gdo-main">
		  <?=$page->topTabs->render()?>
		  <?=Website::topResponse()->render()?>
		  <?=$page->html?>
		</div>
	  </div>

	  <footer id="gdo-footer"><?=$page->bottomNav->render()?></footer>
	
	</div>
	
	<?=Javascript::displayJavascripts(Module_Core::instance()->cfgMinifyJS() === 'concat')?>
  </body>
</html>
