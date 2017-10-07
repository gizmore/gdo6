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
  <link rel="stylesheet" href="../GDO/Core/css/gdo6.css" />
  <link rel="stylesheet" href="../GDO/Core/css/gdo6-classic.css" />
</head>
<body>
  <header>
    <h1>GDO6 Setup</h1>
    <?= GDT_Template::php('Install', 'crumb/progress.php'); ?>
  </header>
  <div class="gdo-body">
    <main><?= $page->html; ?></main>
  </div>
  <footer>
    &copy;2017, 2018 by <?= GDT_Email::make()->val('Christian <gizmore@wechall.net>')->renderCell(); ?>
    <?= GDT_Divider::make()->renderCell(); ?>
    <?= GDT_PerfBar::make()->renderCell(); ?>
  </footer>
</body>
</html>
