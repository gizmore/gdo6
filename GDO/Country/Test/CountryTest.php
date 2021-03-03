<?php
namespace GDO\Country\Test;

use GDO\Tests\TestCase;
use GDO\Country\GDO_Country;
use function PHPUnit\Framework\assertTrue;
use GDO\DB\Database;
use function PHPUnit\Framework\assertEquals;

/**
 * This test also tests @{GDO->allCached()}.
 * @author gizmore
 */
final class CountryTest extends TestCase
{
    public function testCountries()
    {
        $countries = GDO_Country::table()->allCached();
        assertTrue(count($countries) > 200);
        $before = Database::$QUERIES;
        $countries = GDO_Country::table()->allCached();
        $after = Database::$QUERIES;
        assertEquals($before, $after, "Make sure countries use allCached() properly.");
    }
    
}
