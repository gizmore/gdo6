<?php
use GDO\Core\GDT_Template;
use GDO\Core\GDO_Module;
$field instanceof GDT_Template;
$module = $field->gdo;
$module instanceof GDO_Module;
$name = $module->getName();
$checked = isset($_POST['form']['module'][$name]) || $module->defaultEnabled();
$checked = $checked ? 'checked="checked"' : '';
?>
<input
 type="checkbox"
 name="module[<?= $name; ?>]"
 <?= $checked ?> />
