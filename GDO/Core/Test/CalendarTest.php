<?php
namespace GDO\Core\Test;

use GDO\Tests\TestCase;
use GDO\Date\Time;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringNotContainsString;

/**
 * Date and time tests.
 * - check millisecond support
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class CalendarTest extends TestCase
{
    /**
     * Test if milliseconds are supported in dates.
     */
    public function testMilliseconds()
    {
        $date = Time::getDate();
        assertStringContainsString(".", $date);
        assertStringNotContainsString(".000", $date); # yaya... this can give a false positive fail. 1 permille chance :)
    }
    
}
