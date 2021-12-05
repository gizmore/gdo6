<?phpnamespace GDO\Install\thm\install\UI\tpl;use GDO\UI\GDT_Page;use GDO\Core\GDT_Template;
use GDO\UI\GDT_Divider;
use GDO\Perf\GDT_PerfBar;
use GDO\Mail\GDT_Email;
use GDO\Core\Website;use GDO\Language\Trans;
/** @var $page GDT_Page **/ 
?>
<!DOCTYPE html>
<html lang="<?=Trans::$ISO?>">
<head>
  <link rel="stylesheet" href="../install/gdo6-install.css" />
</head>
<body>
  <header>
	<h1>GDO6 Setup</h1>
	<?=GDT_Template::php('Install', 'crumb/progress.php')?>
  </header>
  <div class="gdo-body">
	<div class="gdo-main">
	  <?=Website::topResponse()->render()?>
	  <?=$page->html?>
	</div>
  </div>
  <footer>
	&copy;2017-2022 <?=GDT_Email::make()->var('Christian <gizmore@wechall.net>')->render()?>
	<?=GDT_Divider::make()->renderCell()?>
	<?=GDT_PerfBar::make()->renderCell()?>
  </footer>
</body>
</html>
