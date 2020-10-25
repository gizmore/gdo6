<?php
use GDO\UI\GDT_Bar;
/** @var $bar GDT_Bar **/
foreach ($bar->getFields() as $gdt)
{
	echo $gdt->render();
}
