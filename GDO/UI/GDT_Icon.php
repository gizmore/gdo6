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

    /**
     * Loaded icon providers change this
     * @var callable
     */
    public static $iconProvider = ['GDO\UI\GDT_IconUTF8', 'iconS'];
}
