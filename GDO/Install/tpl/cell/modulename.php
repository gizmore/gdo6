<?php
use GDO\Core\GDT_Template;
use GDO\Core\GDO_Module;
$field instanceof GDT_Template;
$module = $field->gdo;
$module instanceof GDO_Module;
$name = $module->getName();
if ($module->isSiteModule()) {
	$class = 'site-module';
}
elseif ($module->isCoreModule()) {
	$class = 'core-module';
}
else {
	$class = 'gdo-module';
}

if ($module->isInstalled()) {
	$class .= ' module-installed';
}
?>
<span class="<?=$class?>"><?=$name?></span>
