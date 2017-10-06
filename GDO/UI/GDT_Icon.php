<?php
namespace GDO\UI;
use GDO\Core\GDT;
/**
 * Just a single icon.
 * @see WithIcon
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_Icon extends GDT
{
    use WithIcon;

    public static $iconProvider = ['GDO\UI\GDT_Icon', 'utf8Icon'];
}
