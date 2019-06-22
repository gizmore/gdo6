<?php
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Divider;
use GDO\Perf\GDT_PerfBar;
use GDO\Mail\GDT_Email;
$page instanceof GDO\UI\GDT_Page;
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="../GDO/Core/css/gdo6-core.css" />
  <link rel="stylesheet" href="../GDO/Classic/css/gdo6-classic.css" />
  <link rel="stylesheet" href="../GDO/Install/css/install6.css" />
</head>
<body>
  <header>
	<h1>GDO6 Setup</h1>
	<?= GDT_Template::php('Install', 'crumb/progress.php'); ?>
  </header>
  <div class="gdo-body">
	<div class="gdo-main"><?= $page->html; ?></div>
  </div>
  <footer>
	&copy;2017, 2018 by <?= GDT_Email::make()->val('Christian <gizmore@wechall.net>')->renderCell(); ?>
	<?= GDT_Divider::make()->renderCell(); ?>
	<?= GDT_PerfBar::make()->renderCell(); ?>
  </footer>
</body>
</html>
