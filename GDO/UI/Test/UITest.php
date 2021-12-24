<?php
namespace GDO\UI\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Button;
use function PHPUnit\Framework\assertStringContainsString;
use GDO\Form\GDT_Form;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;

final class UITest extends TestCase
{
    public function testButtons()
    {
        $btn = GDT_Button::make()->href(hrefDefault());
        $html = $btn->render();
        assertStringContainsStringIgnoringCase(GDO_MODULE, $html, "Test if Button renders without name.");
        
        $form = GDT_Form::make();
        $form->addField($btn);
        $html = $form->render();
        assertStringContainsString('gdt-button', $html, "Test if Button renders without name inside forms.");
    }
    
}
