<?php /** @var $bar \GDO\UI\GDT_Bar **/ ?>
<div class="gdo-bar gdo-bar-<?= $bar->htmlDirection(); ?>">
<?php if ($bar->fields) : ?>
  <?php foreach ($bar->fields as $field) : ?>
    <?= $field->render(); ?>
  <?php endforeach; ?>
<?php endif;?>
</div>
