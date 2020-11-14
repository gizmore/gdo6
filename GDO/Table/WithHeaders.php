<?php
namespace GDO\Table;

use GDO\Core\GDT_Fields;
use GDO\DB\ArrayResult;
use GDO\Util\Common;
use GDO\Core\GDT;
use GDO\Core\GDO;

/**
 * - A trait for tables and list which adds an extra headers variable. This has to be a \GDO\Core\GDT_Fields.
 * - Implements @\GDO\Core\ArrayResult multisort for use in @\GDO\Table\MethodTable.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.05
 */
trait WithHeaders
{
	###############
	### Headers ###
	###############
	/**
	 * @var GDT_Fields
	 */
	public $headers;
	private function makeHeaders() { if (!$this->headers) $this->headers = GDT_Fields::make(self::nextOrderName()); return $this->headers; }
	public function addHeaders(array $fields) { return count($fields) ? $this->makeHeaders()->addFields($fields) : $this; }
	public function addHeader(GDT $field) { return $this->makeHeaders()->addField($field); }
	
	##############################
	### REQUEST container name ###
	##############################
	private static $ORDER_NAME = 0;
	public static function nextOrderName()
	{
		self::$ORDER_NAME++;
		return "o" . self::$ORDER_NAME;
	}
	
	###############
	### Ordered ###
	###############
	/**
	 * PHP Sorting is unstable.
	 * This method does a stable multisort on an ArrayResult.
	 * @param ArrayResult $result
	 * @return ArrayResult
	 */
	public function multisort(ArrayResult $result, $defaultOrder=null, $defaultOrderAsc=true)
	{
		# Get order from request
	    if ($orders = Common::getRequestArray($this->headers->name))
	    {
	        $orders = @$orders['o'];
	    }
	    
	    if (empty($orders) && $defaultOrder)
	    {
	        $order = $defaultOrderAsc ? '1' : '0';
	        $orders[$defaultOrder] = $order;
	        $_REQUEST[$this->headers->name]['o'] = $orders;
	    }
		
		# Build sort func
		$sort = $this->make_cmp($orders);
		
		# Use it
		usort($result->data, $sort);
		
		return $result;
	}
	
	private function make_cmp(array $sorting)
	{
		$headers = $this->headers;
		return function (GDO $a, GDO $b) use (&$sorting, &$headers)
		{
			foreach ($sorting as $column => $sortDir)
			{
			    if ($gdt = $headers->getField($column))
			    {
    			    if ($diff = $gdt->gdoCompare($a, $b))
    			    {
    					return $sortDir ? $diff : -$diff;
    			    }
			    }
			}
			return 0;
		};
	}
	
}
