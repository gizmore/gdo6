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
<div class="gdo-table">
  <?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
  <?php endif; ?>
  <table id="gwfdt-<?=$field->name?>" class="gdo-table">
	<thead>
	  <tr>
	  <?php foreach($headers as $gdoType) : ?>
		<th class="<?=$gdoType->htmlClass()?>">
		  <label>
			<?= $gdoType->renderHeader(); ?>
			<?php if ($field->ordered) : ?>
			<?= $gdoType->displayTableOrder($field); ?>
			<?php endif; ?>
		  </label>
		  <?php if ($field->filtered) : ?>
			<?= $gdoType->renderFilter($headers->name); ?>
		  <?php endif; ?>
		</th>
	  <?php endforeach; ?>
	  </tr>
	</thead>
	<tbody>
	<?php while ($gdo = $result->fetchAs($field->fetchAs)) : ?>
	<tr data-gdo-id="<?= $gdo->getID()?>">
	  <?php foreach($headers as $gdoType) :
// 	  $col = $field->getField($gdoType->name);
// 	  $gdoType = $col ? $col : $gdoType;
	  $gdoType->gdo($gdo); ?>
		<td class="<?=$gdoType->htmlClass()?>"><?= $gdoType->renderCell(); ?></td>
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
