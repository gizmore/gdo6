<?php /** @var $field \GDO\Form\GDT_Select **/ ?>
<md-input-container class="md-block md-float md-icon-left<?=$field->classError()?>" flex>
  <label><?= $field->displayLabel(); ?></label>
  <md-select
<?php if ($field->multiple) : ?>
   multiple
<?php endif; ?>
   ng-controller="GDOSelectCtrl"
   ng-model="selection"
   ng-init='init(<?=$field->displayJSON()?>)'
   ng-change="multiValueSelected('#gwfsel_<?= $field->name; ?>')"
   <?= $field->htmlDisabled(); ?>>
   <?php if ($field->emptyLabel) : ?>
   <md-option value="<?= $field->emptyValue; ?>"><?= $field->emptyLabel; ?></md-option>
   <?php endif; ?>
	<?php foreach ($field->choices as $value => $choice) : ?>
	  <md-option value="<?= htmlspecialchars($value); ?>">
		<?= $field->renderChoice($choice); ?>
	  </md-option>
	<?php endforeach; ?>
  </md-select>
  <div class="gdo-form-error"><?= $field->error; ?></div>
  <input
   class="n"
   type="hidden"
   <?php echo $field->htmlAttributes(); ?>
   id="gwfsel_<?= $field->name; ?>"
   value="<?= html($field->getSelectedVar()); ?>"
   name="form[<?= $field->name?>]" />
</md-input-container>
