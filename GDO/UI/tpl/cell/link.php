<?php /** @var $link \GDO\UI\GDT_Link **/ ?>
<?php $link->attr('class','gdo-link'); ?>
<span class="gdt-link">
  <?=$link->htmlIcon()?>
  <a <?=$link->htmlAttributes()?>"<?=$link->htmlTarget()?> href="<?=$link->href?>"><?=$link->displayLabel()?></a>
</span>
