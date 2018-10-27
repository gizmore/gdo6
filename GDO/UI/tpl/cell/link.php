<?php /** @var $link \GDO\UI\GDT_Link **/ ?>
<?php $link->attr('class','gdo-link'); ?>
<a <?=$link->htmlAttributes()?>"<?=$link->htmlTarget()?> href="<?=$link->href?>"><?=$link->displayLabel()?><?=$link->htmlIcon()?></a>