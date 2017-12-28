<?php
use GDO\UI\GDT_Color;
use GDO\UI\Color;
$field instanceof GDT_Color;
$fg =  Color::fromHex($field->getValue())->complementary()->asHex();
printf('<span style="background: %1$s; color: %2$s">%1$s</span>', $field->getValue(), $fg);
