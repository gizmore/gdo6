<?php

/** @var $field \GDO\Table\GDT_List **/
$result = $field->getResult();

$pagemenu = $field->getPageMenu();
$pages = $pagemenu->render();
?>

<?=$pages?>
<div class="gdo-list-card">
  <h3 class="title"><?= $field->title; ?></h3>
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
