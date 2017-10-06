<?php
use GDO\UI\GDT_Button;

$field instanceof GDT_Button;
?>
<a class="md-button md-secondary gdo-button"
 <?= $field->htmlDisabled(); ?>
 <?= $field->htmlHREF(); ?>>
  <?= $field->htmlIcon(); ?>
  <?= $field->displayLabel(); ?>
</a>
