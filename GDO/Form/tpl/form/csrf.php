<?php
use GDO\Form\GDT_AntiCSRF;
$field instanceof GDT_AntiCSRF;
?>
<div>
<input
 type="hidden"
 name="form[<?= $field->name; ?>]"
 value="<?= $field->csrfToken(); ?>"></input>
  <div class="gdo-form-error"><?= $field->error; ?></div>
</div>
