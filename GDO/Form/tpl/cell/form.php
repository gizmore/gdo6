<?php
use GDO\File\GDT_File;

/** @var $form \GDO\Form\GDT_Form **/
$firstEditableField = null;
?>
<div class="gdo-form <?=$form->htmlClassSlim()?>">

  <form <?=$form->htmlID()?>
   action="<?=$form->action?>"
   method="<?=$form->method?>"
   enctype="<?=$form->encoding?>">

    <?php if ($form->method === 'GET') : ?>
      <?=$form->htmlHidden()?>
    <?php endif; ?>

<?php if ($form->hasTitle()) : ?>
    <div class="gdo-form-head">
      <h2 class="gdo-form-title"><?=$form->renderTitle()?></h2>
    </div>
<?php endif; ?>

<?php if ($form->info) : ?>
      <p><?= $form->info; ?></p>
<?php endif; ?>

<?php if ($form->hasFields()) : ?>
<?php if ($form->hasVisibleFields()) : ?>
    <div class="gdo-form-inner">
<?php endif; ?>
<?php foreach ($form->getFields() as $field) : ?>
     <?php if ( ($field->editable) && ($field->getVar() === null) && ($field->focusable) && (!$field instanceof GDT_File) ) $firstEditableField = $firstEditableField ? $firstEditableField : $field; ?>
      <?= $field->gdo($form->gdo)->renderForm(); ?>
<?php endforeach; ?>
<?php if ($form->hasVisibleFields()) : ?>
	</div>
<?php endif; ?>
<?php endif; ?>

<?php if ($form->hasActions()) : ?>
	<div class="gdo-form-actions">
      <?=$form->actions()->renderCell()?>
	</div>
<?php endif; ?>

  </form>
</div>
<script type="text/javascript">
window.GDO_FIRST_EDITABLE_FIELD = window.GDO_FIRST_EDITABLE_FIELD||'<?=$firstEditableField?$firstEditableField->id():null?>';
</script>
