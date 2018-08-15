<?php
use GDO\Install\Config;
use GDO\DB\GDT_Checkbox;
use GDO\UI\GDT_Panel;
?>
<h2><?= t('install_title_2'); ?></h2>

<table>
<?php
$valid = true;
foreach ($tests as $i => $test)
{
	printf("<tr><td>%s</td><td>%s</td></tr>", t("install_test_$i"), GDT_Checkbox::make()->val($test)->renderCell());
	$valid = $valid ? $test : false;
}
?>
</table>

<?php
if ($valid)
{
	echo GDT_Panel::make()->html(t('install_system_ok', [Config::linkStep(3)]))->render();
}
else 
{
	echo GDT_Panel::make()->html(t('install_system_not_ok', [Config::linkStep(2)]))->render();
}
