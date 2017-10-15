<?php /** @var $form \GDO\Form\GDT_Form **/ ?>
<!-- Begin Form -->
<div class="gdo-form">
  <div class="md-whiteframe-8dp">
    <div class="gdo-form-inner">
      <div class="gdo-form-head">
        <h2 class="gdo-form-title"><?= $form->title; ?></h2>
        <p><?= $form->info; ?></p>
      </div>
      <form
       id="form_<?=$form->name;?>"
       action="<?= $form->action; ?>"
       method="<?= $form->method; ?>"
       enctype="<?= $form->encoding; ?>">
      <?php foreach ($form->getFields() as $field) : ?>
        <?php if ($field->writable) : ?>
          <?= $field->renderForm(); ?>
        <?php endif; ?>
      <?php endforeach; ?>
      </form>
    </div>
  </div>
</div>
<!-- End of Form -->
