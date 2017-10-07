<?php
use GDO\Date\GDT_Timestamp;
$field instanceof GDT_Timestamp;
$id = 'date_'.$field->name;
?>
<div class="gdo-container<?= $field->classError(); ?>"
 ng-controller="GDODatepickerCtrl">
  <?= $field->htmlIcon(); ?>
  <?= $field->htmlTooltip(); ?>
  <label for="<?=$id;?>"><?=$field->label;?></label>
  <input
   id="<?= $id; ?>"
   type="datetime"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->displayVar(); ?>" />
  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>
 