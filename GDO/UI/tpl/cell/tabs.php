<?php
use GDO\UI\GDT_Tabs;
$field instanceof GDT_Tabs;
?>
<md-tabs md-dynamic-height md-border-bottom>
<?php foreach ($field->getTabs() as $tab) : ?>
<?php if ($cell) : ?>
<?= $tab->renderCell(); ?>
<?php endif; ?>
<?= $tab->renderForm(); ?>
<?php endforeach; ?>
</md-tabs>
