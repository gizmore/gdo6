<?php
use GDO\Table\GDT_List;
use GDO\Util\Common;
$field instanceof GDT_List;
?>
<?php
$result = $field->getResult();
?>
<div class="gdo-list-card">
  <div class="title"><?= $field->label; ?></div>
  <ul>
    <li>
  <?php
$template = $field->getItemTemplate();
while ($gdo = $result->fetchObject())
{
	echo $template->gdo($gdo)->renderCard();
}
?>
    </li>
  </ul>
</div>

<!-- Filter Dialog -->
<div style="visibility: hidden">
  <div class="md-dialog-container" id="gdo-filter-dialog">
    <md-dialog aria-label="Mango (Fruit)">
      <md-dialog-content style="max-width:800px;max-height:810px; ">
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="Filters">
            <md-content class="md-padding">
              <form method="get" action="<?= $field->href ?>">
<?php if ($field->fields) : ?>
<?php foreach ($field->fields as $gdoType) : ?>
                <md-input-container>
                  <label><?= $gdoType->label; ?></label>
                  <?= $gdoType->renderFilter(); ?>
                </md-input-container>
<?php endforeach; ?>
<?php endif; ?>
                <input type="hidden" name="mo" value="<?= html(Common::getGetString('mo')); ?>">
                <input type="hidden" name="me" value="<?= html(Common::getGetString('me')); ?>">
                <input type="submit" class="n" />
              </form>
            </md-content>
          </md-tab>
          <md-tab label="Sorting">
            <md-content class="md-padding">
<?php if ($field->fields) : ?>
<?php foreach ($field->fields as $gdoType) : ?>
              <label><?= $gdoType->label; ?></label>
              <?= $gdoType->displayTableOrder($field)?>
<?php endforeach; ?>
<?php endif; ?>
            </md-content>
          </md-tab>
        </md-tabs>
      </md-dialog-content>
    </md-dialog>
  </div>
</div>
<!-- End Filter Dialog -->
