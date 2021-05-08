<?php
use GDO\Install\Config;
use GDO\UI\GDT_Panel;
?>
<h2><?= t('install_title_4'); ?></h2>
<?php
$email = GDO_ADMIN_EMAIL;
$path = GDO_PATH . 'gdo_cronjob.sh > /dev/null';
$path2 = GDO_PATH . 'gdo_update.sh > /dev/null';
$content = <<<EOC
<br/>
<gdo-code><br/>
MAILTO={$email}<br/>
<br/>
* * * * * {$path}<br/>
30 0 * * * {$path2}<br/>
</gdo-code><br/>
EOC;
echo GDT_Panel::make()->text('install_cronjob_info', [$content, Config::linkStep(6)])->render();
