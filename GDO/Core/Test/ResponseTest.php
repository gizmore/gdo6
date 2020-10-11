<?php
namespace GDO\Core\Test;

use GDO\Core\GDT_Response;
use GDO\UI\GDT_Paragraph;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\UI\GDT_Container;

final class ResponseTest extends TestCase
{
    public function testRendersNestedFields()
    {
        $r1 = GDT_Response::make();
        $p1 = GDT_Paragraph::withHTML('par1');
        $r1->addField($p1);
        $r2 = GDT_Response::make();
        $p2 = GDT_Paragraph::withHTML('par2');
        $r2->addField($p2);
        $r1->add($r2);
        $c = GDT_Container::make();
        $p3 = GDT_Paragraph::withHTML('par3');
        $c->addField($p3);
        $r2->addField($c);
        
        $html = $r1->render();
        
        assertStringContainsString('par1', $html);
        assertStringContainsString('par2', $html);
        assertStringContainsString('par3', $html);
    }

}
