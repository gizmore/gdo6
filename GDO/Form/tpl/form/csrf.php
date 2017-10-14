<?php
use GDO\Form\GDT_AntiCSRF;
$field instanceof GDT_AntiCSRF;
?>
<div class="gdo-container<?=$field->classError()?>">
  <input
   type="hidden"
   name="form[<?= $field->name; ?>]"
   value="<?= $field->csrfToken(); ?>"></input>
  <?= $field->htmlError(); ?>
</div>
