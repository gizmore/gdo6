<?php use GDO\Form\GDT_Form;
/** @var $field \GDO\Table\GDT_Table **/
/** @var $form GDT_Form **/
$headers = $field->getHeaderFields();
if ($pagemenu = $field->getPageMenu())
{
	echo $pagemenu->renderCell();
}
$result = $field->getResult();
?>
<?php if (!$form) : ?>
<form method="get" action="<?= $field->href; ?>" class="b">
<?=GDT_Form::hiddenMoMe()?>
<?php endif; ?>
<div class="gdo-table" id="<?=$field->name?>">
  <?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
  <?php endif; ?>
  <table id="gwfdt-<?=$field->name?>">
	<thead>
	  <tr>
	  <?php foreach($headers as $gdoType) : ?>
	  <?php if (!$gdoType->hidden) : ?>
		<th class="<?=$gdoType->htmlClass()?>">
		  <label>
			<?= $gdoType->renderHeader(); ?>
			<?php if ($field->ordered) : ?>
			<?= $gdoType->displayTableOrder($field); ?>
			<?php endif; ?>
		  </label>
		  <?php if ($field->filtered) : ?>
			<?= $gdoType->renderFilter($field->headers->name); ?>
		  <?php endif; ?>
		</th>
      <?php endif;?>
	  <?php endforeach; ?>
	  </tr>
	</thead>
	<tbody>
	<?php while ($gdo = $result->fetchAs($field->fetchAs)) : ?>
	<tr data-gdo-id="<?= $gdo->getID()?>">
	  <?php foreach($headers as $gdoType) :
// 	  $col = $field->getField($gdoType->name);
// 	  $gdoType = $col ? $col : $gdoType;
	  if (!$gdoType->hidden) :
	  $gdoType->gdo($gdo); ?>
		<td class="<?=$gdoType->htmlClass()?>"><?= $gdoType->renderCell(); ?></td>
	  <?php endif; ?>
	  <?php endforeach; ?>
	</tr>
	<?php endwhile; ?>
	</tbody>
<?php if ($field->footer) : ?>
	<tfoot><?=$field->footer?></tfoot>
<?php endif; ?>
  </table>
  <input type="submit" class="n" />
</div>
<?php if ($actions = $field->getActions()) : ?>
<?php echo $actions->render(); ?>
<?php endif; ?>
<?php if (!$form) : ?>
</form>
<?php endif; ?>
<!-- END of GDT_Table -->
