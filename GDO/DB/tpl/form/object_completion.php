<?php /** @var $field \GDO\DB\GDT_Object **/
$id = 'gwfac_'.$field->name; ?>
<div class="gdo-container<?= $field->classError(); ?>"
 ng-app="gdo6"
 ng-controller="GDOAutoCompleteCtrl"
 ng-init='init(<?= $field->displayJSON(); ?>, "#<?= $id; ?>")'>
  <?= $field->htmlIcon(); ?>
  <label for="form[<?= $field->name; ?>]"><?= $field->label; ?></label>
  <input
   type="text"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>
   id="<?= $id; ?>"
   name="form[<?= $field->name; ?>]"
   value="<?= html($field->getVar()); ?>" />
  <input type="hidden" name="nocomplete" value="1" />
  <?= $field->htmlError(); ?>
</div>
