<?php
use GDO\Util\Common;
use GDO\Table\GDT_Table;
use GDO\Core\GDT;
$field instanceof GDT_Table;

$headers = $field->fields;

if ($pagemenu = $field->getPageMenu())
{
	echo $pagemenu->renderCell();
}
$result = $field->getResult();
?>
<form method="get" action="<?= $field->href; ?>" flex class="b">
<div
 class="gdo-table table-responsive"
 layout="column" flex layout-fill
 ng-controller="GDOTableCtrl"
 ng-init='init(<?= $field->initJSON(); ?>)'>
  <input type="hidden" name="mo" value="<?= html(Common::getGetString('mo','')); ?>" />
  <input type="hidden" name="me" value="<?= html(Common::getGetString('me','')); ?>" />
  <?php if ($field->label) : ?>
  <h3><?= $field->label; ?></h3>
  <?php endif; ?>
  <table id="gwfdt-<?= $field->name; ?>" class="table">
    <thead>
      <tr>
      <?php foreach($headers as $gdoType) : ?>
        <th<?= $gdoType->htmlClass(); ?>>
          <label>
            <?= $gdoType->renderHeader(); ?>
            <?php if ($field->ordered) : ?>
            <?= $gdoType->displayTableOrder($field); ?>
            <?php endif; ?>
          </label>
          <?php if ($field->filtered) : ?>
          <br/><?= $gdoType->renderFilter(); ?>
          <?php endif; ?>
        </th>
      <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php while ($gdo = $result->fetchAs($field->fetchAs)) : ?>
    <tr gdo-id="<?= $gdo->getID()?>">
      <?php foreach($headers as $gdoType) : $gdoType instanceof GDT; ?>
        <td<?= $gdoType->htmlClass(); ?>><?= $gdoType->gdo($gdo)->renderCell(); ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endwhile; ?>
    </tbody>
    <tfoot></tfoot>
  </table>
  <input type="submit" class="n" />
</div>
<?= $field->actions()->renderCell(); ?>
</form>
<!-- END of GDT_Table -->
