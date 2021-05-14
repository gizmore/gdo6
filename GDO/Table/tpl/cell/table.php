<?php use GDO\Form\GDT_Form;
/** @var $field \GDO\Table\GDT_Table **/
/** @var $form GDT_Form **/
$headers = $field->getHeaderFields();
if ($pagemenu = $field->getPageMenu())
{
	echo $pagemenu->render();
}
$result = $field->getResult();
?>
<?php if (!$form) : ?>
<form method="get" action="<?= $field->href; ?>" class="b">
<?=GDT_Form::hiddenMoMe()?>
<?php endif; ?>
<div class="gdt-table" id="<?=$field->name?>">
  <?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
  <?php endif; ?>
  <table id="gwfdt-<?=$field->name?>">
	<thead>
	  <tr>
	  <?php foreach($headers as $gdt) : ?>
	  <?php if (!$gdt->hidden) : ?>
		<th class="<?=$gdt->htmlClass()?>">
		  <label>
			<?= $gdt->renderHeader(); ?>
			<?php if ($field->ordered) : ?>
			<?= $gdt->displayTableOrder($field); ?>
			<?php endif; ?>
		  </label>
		  <?php if ($field->filtered) : ?>
			<?= $gdt->renderFilter($field->headers->name); ?>
		  <?php endif; ?>
		</th>
      <?php endif;?>
	  <?php endforeach; ?>
	  </tr>
	</thead>
	<tbody>
	
	<?php if ($field->fetchInto) : ?>
	<?php $dummy = $result->table->cache->getDummy(); ?>
	<?php while ($gdo = $result->fetchInto($dummy)) : ?>
	<tr data-gdo-id="<?=$gdo->getID()?>">
	  <?php foreach($headers as $gdt) :
	  if (!$gdt->hidden) :
	       $gdt->gdo($gdo); ?>
		<td class="<?=$gdt->htmlClass()?>"><?=$gdt->renderCell()?></td>
	  <?php endif; ?>
	  <?php endforeach; ?>
	</tr>
	<?php endwhile; ?>
	<?php else : ?>
	<?php while ($gdo = $result->fetchAs($field->fetchAs)) : ?>
	<tr data-gdo-id="<?=$gdo->getID()?>">
	  <?php foreach($headers as $gdt) :
	  if (!$gdt->hidden) :
	       $gdt->gdo($gdo); ?>
		<td class="<?=$gdt->htmlClass()?>"><?=$gdt->renderCell()?></td>
	  <?php endif; ?>
	  <?php endforeach; ?>
	</tr>
	<?php endwhile; ?>
	<?php endif; ?>
	
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
