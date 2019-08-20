<?php /** @var $link \GDO\UI\GDT_Link **/ ?>
<span class="<?=$link->htmlClass()?>">
  <?=$link->htmlIcon()?>
  <a
   <?=$link->htmlName()?>
   <?=$link->htmlAttributes()?>
   <?=$link->htmlTarget()?>
   <?=$link->htmlHREF()?>
   href="<?=html($link->href)?>">
    <?=$link->displayLabel()?>
  </a>
</span>
