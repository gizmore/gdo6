<?php
use GDO\Core\GDT_Template;
use GDO\Core\GDO_Module;
$field instanceof GDT_Template;
$module = $field->gdo;
$module instanceof GDO_Module;
$name = $module->getName();
$checked = isset($_REQUEST['module'][$name]) || $module->defaultEnabled();
$checked = $checked ? 'checked="checked"' : '';
?>
<input
 id="cbx-module-<?=$name?>"
 type="checkbox"
 class="gdo-module-install-cbx"
 onclick="toggledModule(this, '<?=$name?>');"
 name="module[<?=$name?>]"
 <?=$checked?> />
