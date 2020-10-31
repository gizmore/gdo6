<?php /** @var $link \GDO\UI\GDT_Link **/ ?>
<span class="<?=$link->htmlClass()?>">
  <?=$link->htmlIcon()?>
  <a
   <?=$link->htmlDisabled()?>
   <?=$link->htmlName()?>
   <?=$link->htmlAttributes()?>
   <?=$link->htmlTarget()?>
   <?=$link->htmlHREF()?>
   <?=$link->htmlRelation()?>><?=$link->displayLabel()?></a>
</span>
