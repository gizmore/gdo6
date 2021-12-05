<?php
use GDO\Install\Config;
use GDO\UI\GDT_Panel;
use GDO\UI\GDT_Container;
/** @var $container GDT_Container **/
?>
<h2><?= t('install_title_5'); ?></h2>
<?php
$email = GDO_ADMIN_EMAIL;
$path = GDO_PATH . 'gdo_cronjob.sh > /dev/null';
$path2 = GDO_PATH . 'gdo_update.sh > /dev/null';
$content = <<<EOC
<br/>
<div class="gdo-code"><br/>
MAILTO={$email}<br/>
<br/>
* * * * * {$path}<br/>
30 0 * * * {$path2}<br/>
EOC;
foreach ($container->getFields() as $gdt)
{
	$content .= $gdt->render();
}
$content .= "</div><br/>\n";

echo GDT_Panel::make()->text('install_cronjob_info', [$content, Config::linkStep(6)])->render();
