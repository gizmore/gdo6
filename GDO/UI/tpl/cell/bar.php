<?php /** @var $bar \GDO\UI\GDT_Bar **/
$bar->addClass('gdt-bar gdt-bar-' . $bar->htmlDirection());
?>
<div <?=$bar->htmlAttributes()?>>
<?php if ($bar->fields) : ?>
  <?php foreach ($bar->fields as $field) : ?>
	<?=$field->render()?>
  <?php endforeach; ?>
<?php endif;?>
</div>
