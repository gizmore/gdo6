<?php /** @var $field \GDO\Form\GDT_Select **/
use GDO\Core\GDO;
use GDO\Util\Arrays;
$val = Arrays::arrayed($field->getValue());
$sel = 'selected="selected"';
?>
<div class="gdo-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label><?= $field->label; ?></label>
  <select
<?php if ($field->multiple) : ?>
   name="form[<?= $field->name?>][]"
   multiple="multiple"
   <?php else : ?>
   name="form[<?= $field->name?>]"
   <?php endif; ?>
   <?= $field->htmlDisabled(); ?>>
   <?php if ($field->emptyLabel) : ?>
   <option value="<?= $field->emptyValue; ?>" <?=$field->htmlSelected((string)$field->emptyValue);?>><?=$field->emptyLabel;?></option>
   <?php endif; ?>
    <?php foreach ($field->choices as $value => $choice) : ?>
      <option value="<?= html($value); ?>" <?=$field->htmlSelected((string)$value);?>><?= $choice instanceof GDO ? $choice->renderChoice() : $choice; ?></option>
    <?php endforeach; ?>
  </select>
  <?= $field->htmlError(); ?>
</div>
