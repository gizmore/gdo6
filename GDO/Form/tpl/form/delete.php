<?php /** @var \GDO\Form\GDT_DeleteButton $field **/ ?>
<input
 type="submit"
 onclick="return confirm('<?=t('confirm_delete')?>')"
 name="<?= $field->name; ?>"
 value="<?= $field->displayLabel(); ?>"
 <?= $field->htmlDisabled(); ?> /></input>
