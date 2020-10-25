<?php
use GDO\Form\GDT_AntiCSRF;
/** @var $field GDT_AntiCSRF **/
?>
<div class="gdo-container<?=$field->classError()?>">
  <input
   type="hidden"
   <?=$field->htmlFormName()?>
   value="<?=$field->csrfToken()?>" />
  <?=$field->htmlError()?>
</div>
