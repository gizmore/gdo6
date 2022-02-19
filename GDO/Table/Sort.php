<?php
namespace GDO\Table;

use GDO\Core\GDO;
use GDO\DB\ArrayResult;

/**
 * Utility class to sort GDOs.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.10.0
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
        $table->headers->name = '_mosort_';
        
        # Plug orders into request vars
        $o = $table->headers->name;
        $_REQUEST[$o] = ['o' => []];
        foreach ($orders as $column => $asc)
        {
            $_REQUEST[$o]['o'][$column] = $asc ? '1' : '0';
        }
        
        # sort the result
        $table->multisort($result);

        # Ugly: fix order name prediction
        unset($_REQUEST[$o]);
        GDT_Table::$ORDER_NAME--;
    }
    
}
