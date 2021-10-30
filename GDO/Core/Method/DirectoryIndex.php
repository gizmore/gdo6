<?php
namespace GDO\Core\Method;

use GDO\Core\Module_Core;
use GDO\Core\GDO_DirectoryIndex;
use GDO\DB\ArrayResult;
use GDO\Table\MethodTable;
use GDO\File\FileUtil;

final class DirectoryIndex extends MethodTable
{
    public function isOrdered() { return false; }
    public function isFiltered() { return false; }
    public function isSearched() { return false; }
    public function isPaginated() { return false; }
    
    public function isAllowed()
    {
        return Module_Core::instance()->cfgDirectoryIndex();
    }
    
    public function execute()
    {
        if (!$this->isAllowed())
        {
            return $this->error('err_no_permission');
        }
        return parent::execute();
    }
    
    public function gdoTable()
    {
        return GDO_DirectoryIndex::table();
    }
    
    public function getTableTitle()
    {
        $key = $this->getTableTitleLangKey();
        return t($key, [$this->table->countItems() - 1]);
    }
    
    public function getResult()
    {
        $data = [];
        $files = scandir($_REQUEST['_url']);
        foreach ($files as $file)
        {
            if ($file === '.')
            {
                continue;
            }
            $path = $_REQUEST['_url'] . '/' . $file;
            $data[] = $this->entry($path, $file);
        }
        return new ArrayResult($data, $this->gdoTable());
    }
    
    private function entry($path, $filename)
    {
        if (is_dir($path))
        {
            return GDO_DirectoryIndex::blank([
                'file_icon' => 'folder',
                'file_name' => $filename,
                'file_type' => 'directory',
            ]);
        }
        else
        {
            return GDO_DirectoryIndex::blank([
                'file_icon' => 'file',
                'file_name' => $filename,
                'file_type' => FileUtil::mimetype($path),
                'file_size' => filesize($path),
            ]);
        }
    }
}
