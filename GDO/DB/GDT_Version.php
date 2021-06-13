<?php
namespace GDO\DB;

/**
 * A version string.
 * Sets step to 0.01 and significant digits to 2,2.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.0.1
 * 
 * @see GDT_Decimal
 */
final class GDT_Version extends GDT_Decimal
{
    protected function __construct()
    {
        parent::__construct();
        $this->digits(2, 2);
        $this->min("1.00");
        $this->max("10.00");
    }

}
