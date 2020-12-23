<?php
namespace GDO\UI\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Message;
use function PHPUnit\Framework\assertTrue;

final class MessageTest extends TestCase
{
    public function testRendering()
    {
        $message = GDT_Message::make()->var('<p><a>Test</a></p>');
        $html = $message->renderCell();
        assertTrue(substr_count($html, '<div') == 1);
    }
    
}
