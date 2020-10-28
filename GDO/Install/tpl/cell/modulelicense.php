<?php
use GDO\Core\GDT_Template;
use GDO\Core\GDO_Module;
/** @var $field GDT_Template **/
/** @var $module GDO_Module **/
$module = $field->gdo;
$class = 'gdt-module-license'
?>
<span class="<?=$class?>"><?=$module->module_license?></span>
