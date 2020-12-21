<?php
namespace GDO\Core\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;
use GDO\Mail\Mail;

final class DebugTest extends TestCase
{
    public function testDebugMail()
    {
        Mail::$DEBUG = 1;
        1 + 'e'; # ouch
    }
    
    public function testDebugMail2()
    {
        assertGreaterThan(0, Mail::$SENT, 'check if debug mails were sent');
    }
    
}
