<?php
namespace GDO\Core\Test;

use GDO\Tests\TestCase;
use GDO\Net\GDT_IP;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertFalse;

final class NetTest extends TestCase
{
    public function testIPLocal()
    {
        assertTrue(GDT_IP::isLocal('::1'), 'Test if IPv6 local IP is detected');
        assertTrue(GDT_IP::isLocal('127.0.0.1'), 'Test if IPv4 127. local IP is detected');
        assertTrue(GDT_IP::isLocal('192.168.0.1'), 'Test if IPv4 192.168 local IP is detected');
        assertTrue(GDT_IP::isLocal('172.16.0.1'), 'Test if IPv4 172.16.x.x local IP is detected');
        assertTrue(GDT_IP::isLocal('172.31.0.1'), 'Test if IPv4 172.31.x.x local IP is detected');
        assertFalse(GDT_IP::isLocal('192.167.0.1'), 'Test if IPv4 192.167. remote IP is detected');
    }

}
