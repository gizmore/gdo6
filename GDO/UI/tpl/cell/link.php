<?php /** @var $link \GDO\UI\GDT_Link **/ ?>
<a class="gdt-link <?=$link->htmlKlass?>"<?=$link->htmlTarget()?> href="<?=$link->href?>"><?=$link->label?><?=$link->htmlIcon()?></a>