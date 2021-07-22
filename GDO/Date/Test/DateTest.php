<?php
namespace GDO\Date\Test;

use GDO\Tests\TestCase;
use GDO\Date\Time;

/**
 * Test date and time.
 * @see Einstein.A
 * @author gizmore
 * @since 6.10.4
 */
final class DateTest extends TestCase
{
    public function testParseDBDate()
    {
        $date = '2021-07-22 13:48:22.123';
        Time::parseDateDB($date);
    }
    
}
