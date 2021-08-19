<?php
use GDO\Install\Config;
use GDO\DB\GDT_Checkbox;
use GDO\UI\GDT_Panel;
?>
<h2><?= t('install_title_2'); ?></h2>

<table>
<tr><td colspan=2><h3><?= t('install_title_2_tests')?></h3></td></tr>
<?php
$valid = true;
foreach ($tests as $i => $test)
{
	printf("<tr><td>%s</td><td>%s</td></tr>\n", t("install_test_$i"), GDT_Checkbox::make()->value($test)->renderCell());
	$valid = $test === false ? false : $valid;
}

?>

<tr><td colspan=2><h3><?= t('install_title_2_optionals')?></h3></td></tr>
<?php
foreach ($optional as $i => $test)
{
	printf("<tr><td>%s</td><td>%s</td></tr>\n", t("install_optional_$i"), GDT_Checkbox::make()->value($test)->renderCell());
}

?>
</table>

<?php
if ($valid)
{
	echo GDT_Panel::make()->textRaw(t('install_system_ok', [Config::linkStep(3)]))->render();
}
else 
{
	echo GDT_Panel::make()->textRaw(t('install_system_not_ok', [Config::linkStep(2)]))->render();
}
