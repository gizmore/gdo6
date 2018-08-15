<?php
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\Util\Math;
use GDO\Util\Common;
use GDO\Install\Config;

$steps = Config::steps();
$step = Math::clamp(Common::getGetInt('step', 1), 1, $steps);

$bar = GDT_Bar::make()->horizontal();

foreach ($steps as $step => $name)
{
	$step++;
	$link = GDT_Link::make("step$step")->href(Config::hrefStep($step));
	$bar->addField($link);
}

echo $bar->renderCell();
