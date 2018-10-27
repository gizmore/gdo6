<?php /** @var $field \GDO\Table\GDT_List **/
use GDO\Util\Common;
$result = $field->getResult();
?>
<div class="gdo-list-card">
  <div class="title"><?= $field->title; ?></div>
  <ul>
	<li>
<?php
$template = $field->getItemTemplate();
while ($gdo = $result->fetchObject())
{
	echo $template->gdo($gdo)->renderCard();
}?>
	</li>
  </ul>
</div>
<?php if ($field->headers && count($field->headers->fields)) : $fields = $field->headers->fields; ?>
<!-- Filter Dialog -->
<div style="visibility: hidden">
  <div class="md-dialog-container" id="gdo-filter-dialog">
	<md-dialog aria-label="Mango (Fruit)">
	  <md-dialog-content style="max-width:800px;max-height:810px; ">
		<md-tabs md-dynamic-height md-border-bottom>
		  <md-tab label="Filters">
			<md-content class="md-padding">
			  <form method="get" action="<?= $field->href ?>">
<?php foreach ($fields as $gdoType) : ?>
				<md-input-container>
				  <label><?= $gdoType->displayLabel(); ?></label>
				  <?= $gdoType->renderFilter(); ?>
				</md-input-container>
<?php endforeach; ?>
				<input type="hidden" name="mo" value="<?= html(Common::getGetString('mo')); ?>">
				<input type="hidden" name="me" value="<?= html(Common::getGetString('me')); ?>">
				<input type="submit" class="n" />
			  </form>
			</md-content>
		  </md-tab>
		  <md-tab label="Sorting">
			<md-content class="md-padding">
<?php foreach ($fields as $gdoType) : ?>
			  <label><?= $gdoType->displayLabel(); ?></label>
			  <?= $gdoType->displayTableOrder($field)?>
<?php endforeach; ?>
			</md-content>
		  </md-tab>
		</md-tabs>
	  </md-dialog-content>
	</md-dialog>
  </div>
</div>
<!-- End Filter Dialog -->
<?php endif; ?>
