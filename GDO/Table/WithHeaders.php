<?php
namespace GDO\Table;

use GDO\Core\GDT_Fields;
use GDO\DB\ArrayResult;
use GDO\Util\Common;
use GDO\Core\GDT;
use GDO\Core\GDO;
use GDO\Util\Strings;
use GDO\Util\Arrays;

/**
 * - A trait for tables and list which adds an extra headers variable. This has to be a \GDO\Core\GDT_Fields.
 * - Implements @\GDO\Core\ArrayResult multisort for use in @\GDO\Table\MethodTable.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.5.0
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
	
	/**
	 * @return GDT_Fields
	 */
	public function makeHeaders() { if ($this->headers === null) $this->headers = GDT_Fields::make($this->nextOrderName()); return $this->headers; }
	public function addHeaders(array $fields) { return count($fields) ? $this->makeHeaders()->addFields($fields) : $this; }
	public function addHeader(GDT $field) { return $this->makeHeaders()->addField($field); }
	
	##############################
	### REQUEST container name ###
	##############################
	public $headerName = null;
	public function headerName($headerName)
	{
		$this->headerName = $headerName;
		return $this;
	}
	
	public static $ORDER_NAME = 0;
	public function nextOrderName()
	{
		return $this->headerName ? $this->headerName : ("o" . (++self::$ORDER_NAME));
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
	public function multisort(ArrayResult $result, $defaultOrder=null)
	{
		# Get order from request
	    if ($orders = Common::getRequestArray($this->headers->name))
	    {
	        $orders = Arrays::arrayed(@$orders['o']);
	    }
	    
	    if (empty($orders) && $defaultOrder)
	    {
	        $col = Strings::substrTo($defaultOrder, ' ', $defaultOrder);
	        $order = stripos($defaultOrder, ' DESC') ? '0' : '1';
	        $orders[$col] = $order;
	        $_REQUEST[$this->headers->name]['o'] = $orders[$col];
	    }
		
		# Build sort func
		$sort = $this->make_cmp($orders);
		
		# Use it
		uasort($result->getData(), $sort);
		
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
