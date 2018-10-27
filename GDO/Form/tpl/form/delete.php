<?php /** @var GDT_DeleteButton $field **/ ?>
<input
 type="submit"
 onsubmit="return confirm('<?=t('confirm_delete')?>')"
 class="md-button md-primary md-raised"
 name="<?= $field->name; ?>"
 value="<?= $field->displayLabel(); ?>"
 <?= $field->htmlDisabled(); ?> /></input>
