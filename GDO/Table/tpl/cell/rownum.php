<?php
use GDO\Table\GDT_RowNum;
$field instanceof GDT_RowNum;
$field->num++;
$id = $num = $field->gdo ? $field->gdo->getID() : $field->num;
$name = "{$field->name}[$id]";
$class = "rbxall-{$field->name}";
$checked = isset($_REQUEST[$field->name][$id]) ? 'checked="checked"' : '';
?>
<input
 type="checkbox"
 <?=$checked?>
 class="<?=$class?>"
 name="<?=$name?>" />
