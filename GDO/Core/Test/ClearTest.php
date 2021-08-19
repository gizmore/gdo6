<?php
namespace GDO\Core\Test;

use GDO\Tests\TestCase;
use GDO\File\FileUtil;
use function PHPUnit\Framework\assertDirectoryExists;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;
use GDO\File\GDO_File;

final class ClearTest extends TestCase
{
    public function testFileUtilToClearFiles()
    {
        $path = GDO_File::filesDir();

        FileUtil::removeDir($path);
        assertDirectoryDoesNotExist($path, 'Test if files/ folder can be deleted.');

        FileUtil::createDir($path);
        assertDirectoryExists($path, 'Test if files/ folder can be re-created.');
    }

}
