<?php /** @var $bar \GDO\UI\GDT_Bar **/
$bar->addClass('gdt-bar flx flx-' . $bar->htmlDirection());
if ($bar->wrap)
{
    $bar->addClass('flx-wrap');
}
?>
<div <?=$bar->htmlID()?> <?=$bar->htmlAttributes()?>>
<?php if ($bar->fields) : ?>
  <?php foreach ($bar->fields as $field) : ?>
	<?=$form ? $field->renderForm() : $field->renderCell()?>
  <?php endforeach; ?>
<?php endif;?>
</div>
