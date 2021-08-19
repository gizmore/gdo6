<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * Print security.txt file.
 *
 * @author gizmore
 * @since 6.10.6
 */
final class Security extends MethodPage
{
    public function isAjax() { return true; }

}
