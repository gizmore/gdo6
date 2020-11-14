<?php
/** @var $field \GDO\Table\GDT_List **/
$result = $field->getResult();

$pagemenu = $field->getPageMenu();
$pages = $pagemenu->render();
?>

<?=$pages?>
<div class="gdo-list-card">
<?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
<?php endif; ?>
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
<?=$pages?>
