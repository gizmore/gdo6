<?php /** @var $form \GDO\Form\GDT_Form **/
$firstEditableField = null;
?>
<!-- Begin Form -->
<div class="gdo-form">
  <div class="md-whiteframe-8dp">
	<div class="gdo-form-inner">
	  <div class="gdo-form-head">
		<h2 class="gdo-form-title"><?= $form->title; ?></h2>
		<p><?= $form->info; ?></p>
	  </div>
	  <form
	   id="gdo_<?=$form->name;?>"
	   action="<?= $form->action; ?>"
	   method="<?= $form->method; ?>"
	   enctype="<?= $form->encoding; ?>">
	  <?php if ($form->method === 'GET') : ?>
		<input type="hidden" name="mo" value="<?=html(mo())?>" />
		<input type="hidden" name="me" value="<?=html(me())?>" />
	  <?php endif; ?>
<?php foreach ($form->getFields() as $field) : ?>
		<?php if ($field->writable) :
if ($field->editable) $firstEditableField = $firstEditableField ? $firstEditableField : $field; ?>
		  <?= $field->renderForm(); ?>
		<?php endif; ?>
<?php endforeach; ?>
	  </form>
	</div>
  </div>
</div>
<script type="text/javascript">
window.GDO_FIRST_EDITABLE_FIELD = window.GDO_FIRST_EDITABLE_FIELD||'<?=$firstEditableField->id()?>';
</script>
<!-- End of Form -->
