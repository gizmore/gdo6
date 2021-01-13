<?php
namespace GDO\Language\Test;

use GDO\Tests\TestCase;
use GDO\Language\Module_Language;
use GDO\Language\Trans;
use function PHPUnit\Framework\assertGreaterThanOrEqual;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertEquals;

/**
 * Configure the Language module for 3 languages.
 * Rudimentary i18n test.
 * @author gizmore
 */
final class LanguageTest extends TestCase
{
    public function testLanguage()
    {
        $module = Module_Language::instance();
        $module->saveConfigVar('languages', '["de","en","it"]');
        $languages = $module->cfgSupported();
        assertGreaterThanOrEqual(3, count($languages), 'Check if 3 languages can be supported via Language config.');
        
        $de1 = tiso('de', 'btn_send');
        $en1 = tiso('en', 'btn_send');
        assertNotEquals($de1, $en1, 'english should differ from german');
        
        Trans::setISO('de');
        $de2 = t('btn_send');
        Trans::setISO('en');
        $en2 = t('btn_send');
        assertNotEquals($de1, $en1, 'german should differ from english');
        assertEquals($de1, $de2, 'german should be identical');
        assertEquals($en1, $en2, 'english should be identical');
    }
    
    public function testHTTPLangDetection()
    {
        $iso = Module_Language::instance()->detectAcceptLanguage();
        assertEquals('de', $iso, 'Test if german language is detected.');
    }
    
}
