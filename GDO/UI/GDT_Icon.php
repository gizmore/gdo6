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
     * Default icon size.
     * @var integer
     */
    const DEFAULT_SIZE = 14;
    
    /**
     * When an icon provider is loaded, it changes this var.
     * @var callable
     */
    public static $iconProvider = ['GDO\UI\GDT_IconUTF8', 'iconS'];
}
