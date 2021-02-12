<?php /** @var \GDO\Form\GDT_DeleteButton $field **/ ?>
<input
 type="submit"
 onclick="return confirm('<?=$field->displayConfirmText()?>')"
 name="<?=$field->formName()?>"
 value="<?= $field->displayLabel(); ?>"
 <?= $field->htmlDisabled(); ?> /></input>
