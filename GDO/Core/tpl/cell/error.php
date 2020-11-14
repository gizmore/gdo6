<?php /** @var $field \GDO\Core\GDT_Error **/
use GDO\UI\GDT_Icon; ?>
<div class="gdo-error gdo-panel">
<?php if ($field->hasTitle()) : ?>
  <h3><?=GDT_Icon::iconS('error')?><?=$field->renderTitle()?></h3>
  <?php if ($field->hasText()) : ?>
  <p><?php $field->renderText()?></p>
  <?php endif; ?>
<?php else : ?>
  <p><?=GDT_Icon::iconS('error')?><?=$field->renderText()?></p>
<?php endif; ?>
</div>
