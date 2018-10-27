<?php /** @var $field \GDO\UI\GDT_Tab **/ ?>
<div class="gdo-tab">
  <div class="title"><?= $field->displayLabel(); ?></div>
  <div class="content">
<?php
foreach ($field->getFields() as $gdoType) :
	echo $cell ? $gdoType->renderCell() : $gdoType->renderForm();
endforeach;?>
  </div>
</div>
