<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * Print the default robots.txt file.
 * If you place your own real file this method becomes unused.
 * 
 * @author gizmore
 * @since 6.10.6
 */
final class Robots extends MethodPage
{
    public function isAjax() { return true; }
    
}
