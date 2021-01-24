<?php
use GDO\Core\GDT_Template;

/** @var $field \GDO\Table\GDT_List **/

echo GDT_Template::php('Table', 'cell/_listfilter.php', ['field' => $field]);

$result = $field->getResult();

$pagemenu = $field->getPageMenu();
$pages = $pagemenu ? $pagemenu->render() : '';
?>

<?=$pages?>
<div class="gdt-list-card">
<?php if ($field->hasTitle()) : ?>
  <h3 class="gdt-headline"><?=$field->renderTitle()?></h3>
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
