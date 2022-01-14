<?php
namespace GDO\Date\Test;

use GDO\Tests\TestCase;
use GDO\Date\Time;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

/**
 * Test date and time.
 * @see Einstein.A
 * @author gizmore
 * @since 6.10.4
 */
final class DateTest extends TestCase
{
    public function testPHP()
    {
        $timezone = new \DateTimeZone('Europe/Berlin');
        $dt = \DateTime::createFromFormat('m/d/Y H:i:s.u', '01/05/2020 22:01:25.000', $timezone);
        assertTrue(!!$dt, 'Test if PHP datetime parsing is ok');
    }
    
    public function testParseDBDate()
    {
        $this->lang('de');
        $this->timezone('Europe/Berlin');
        $date = '2021-07-22 13:48:22.123';
        $time = Time::parseDateDB($date);
        $back = Time::getDate($time);
        assertEquals($date, $back, 'Test DB date conversion to Timestamp and back');
        assertEquals('1626961702.123', sprintf('%.03f', $time), 'Test if DB dates can be parsed');
    }
    
    public function testGermanInput()
    {
        $this->lang('de');
        $this->timezone('Europe/Berlin');
     
        $date = '10.04.2021';
        $time = Time::parseDate($date);
        assertEquals('1618005600.000', sprintf('%.03f', $time), 'Test if german year date can be parsed');
        
        $date = '10.04.2021 18:40';
        $time = Time::parseDate($date);
        assertEquals('1618072800.000', sprintf('%.03f', $time), 'Test if german minute date can be parsed');
        
        $date = '10.04.2021 18:40:23';
        $time = Time::parseDate($date);
        assertEquals('1618072823.000', sprintf('%.03f', $time), 'Test if german second date can be parsed');
        
        $date = '10.04.2021 18:40:23.123';
        $time = Time::parseDate($date);
        assertEquals('1618072823.123', sprintf('%.03f', $time), 'Test if german ms date can be parsed');
    }
    
    public function testEnglishInput()
    {
        $this->lang('en');
        $this->timezone('America/New_York');
        
        $date = '04/10/2021';
        $time = Time::parseDate($date);
        assertEquals('1618027200.000', sprintf('%.03f', $time), 'Test if US year date can be parsed');
        
        $date = '04/10/2021 18:40';
        $time = Time::parseDate($date);
        assertEquals('1618094400.000', sprintf('%.03f', $time), 'Test if US minute date can be parsed');
        
        $date = '04/10/2021 18:40:23';
        $time = Time::parseDate($date);
        assertEquals('1618094423.000', sprintf('%.03f', $time), 'Test if US second date can be parsed');
        
        $date = '04/10/2021 18:40:23.123';
        $time = Time::parseDate($date);
        assertEquals('1618094423.123', sprintf('%.03f', $time), 'Test if US ms date can be parsed');
    }

    public function testDisplayDate()
    {
        $this->lang('de');
        $this->timezone('Europe/Berlin');
        $dbdate = '2021-11-09 08:00:59.123';
        $result = Time::displayDate($dbdate);
        assertEquals('09.11.2021 09:00', $result);
    }
    
    public function testHumanDuration()
    {
    	# Test vector
    	$tx = Time::ONE_WEEK*2 + Time::ONE_DAY*3;
    	
    	# To human
    	$hd = Time::humanDuration($tx);
    	assertEquals("2w 3d", $hd, "Test if integers convert to human duration.");
    	
    	# To int
    	$t = Time::humanToSeconds($hd);
    	assertEquals($tx, $t, "Test if human duration converts to seconds.");
    }
    
}
