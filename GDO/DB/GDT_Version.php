<?php
namespace GDO\DB;
/**
 * A version string.
 * @author gizmore
 * @version 7.00
 * @since 6.01
 */
class GDT_Version extends GDT_Decimal
{
	public $digitsBefore = 2;
	public $digitsAfter = 2;
	public $step = 0.01;
}
