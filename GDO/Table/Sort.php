<?php
namespace GDO\Table;

use GDO\Core\GDO;
use GDO\DB\ArrayResult;

/**
 * Utility class to sort GDOs.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class Sort
{
    /**
     * @param GDO[] $array
     * @param GDO $table
     * @param bool[string] $orders
     */
    public static function sortArray(array &$array, GDO $table, array $orders)
    {
        $result = new ArrayResult($array, $table);
        self::sortResult($result, $orders);
    }
    
    /**
     * Sort a result set, stable, by multiple columns.
     * @param ArrayResult $result
     * @param array $orders
     */
    public static function sortResult(ArrayResult $result, array $orders)
    {
        # Create a table to sort with
        $table = GDT_Table::make('sort_table');
        $table->addHeaders($result->table->gdoColumnsCache());
        
        # Plug orders into request vars
        $o = $table->headers->name;
        $_REQUEST[$o] = ['o' => []];
        foreach ($orders as $column => $asc)
        {
            $_REQUEST[$o]['o'][$column] = $asc ? '1' : '0';
        }
        
        GDT_Table::$ORDER_NAME--; # Ugly: fix order name prediction
        
        # sort the result
        $table->multisort($result);
    }
    
}
