<?php
use GDO\UI\GDT_Tab;
$field instanceof GDT_Tab;
?>
<md-tab label="<?= $field->displayLabel(); ?>">
  <md-content class="md-padding">
<?php
foreach ($field->getFields() as $gdoType)
{
	echo $cell ? $gdoType->renderCell() : $gdoType->renderForm();
}
?>
  </md-content>
</md-tab>
