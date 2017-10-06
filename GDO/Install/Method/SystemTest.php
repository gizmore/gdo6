<?php
namespace GDO\Install\Method;

use GDO\Core\Method;
use GDO\File\FileUtil;

/**
 * Do some tests and output in page.
 * @author gizmore
 */
final class SystemTest extends Method
{
    public function execute()
    {
        $tVars = array(
            'tests' => array(
                FileUtil::createDir(GWF_PATH . 'protected'),
                FileUtil::createDir(GWF_PATH . 'files'),
                FileUtil::createDir(GWF_PATH . 'temp'),
                $this->testBower(),
            ),
            'optional' => array(
            ),
        );
        return $this->templatePHP('page/systemtest.php', $tVars);
    }
    
    private function testBower()
    {
        return false;
    }
    
}
