<?php
use GDO\Table\GDT_RowNum;
$field instanceof GDT_RowNum;
$field->num++;
$id = $num = $field->gdo ? $field->gdo->getID() : $field->num;
$name = "{$field->name}[$id]";
?>
<div ng-controller="GDOCbxCtrl">
  <input
   type="checkbox"
   name="<?= $name ?>" />
</div>
