<?php /** @var $field \GDO\Table\GDT_List */ 
use GDO\Util\Common;
$headers = $field->headers;
?>
<!-- List -->
<div class="gdo-list">
<?php if ($field->title) : ?>
  <h3><?= $field->title; ?></h3>
<?php endif; ?>
  <ul>
<?php
$result = $field->getResult();
$template = $field->getItemTemplate();
while ($gdo = $result->fetchObject()) :
	echo $template->gdo($gdo)->renderList();
endwhile; ?>
  </ul>
</div>

<?= $field->pagemenu ? $field->pagemenu->renderCell() : ''; ?>
<!-- End of List -->

<?php if ($headers && count($headers->fields)) : ?>
<!-- Filter Dialog -->
<div style="visibility: hidden">
  <div class="md-dialog-container" id="gdo-filter-dialog">
    <md-dialog aria-label="Mango (Fruit)">
      <md-dialog-content style="max-width:800px;max-height:810px; ">
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="Filters">
            <md-content class="md-padding">
              <form method="get" action="<?= $field->href ?>">
<?php foreach ($headers->fields as $gdoType) : ?>
                  <label><?= $gdoType->label; ?></label>
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
<?php foreach ($headers->fields as $gdoType) : ?>
              <label><?= $gdoType->label; ?></label>
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
