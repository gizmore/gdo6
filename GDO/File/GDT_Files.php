<?php
namespace GDO\File;

use GDO\Util\Arrays;

/**
 * @author gizmore
 * @since 6.08
 * @version 6.08
 */
class GDT_Files extends GDT_File
{
	public function gdoColumnDefine() { return null; }
	
	public function getGDOData() { return null; } # Only relation table. Handled by onCreate and onUpdate.
	
	public function toVar($value)
	{
		return null;
	}
	
	
	
	public $fileTable;
	public $fileObjectTable;
	public function fileTable(GDO_FileTable $table)
	{
		$this->fileTable = $table;
		$this->fileObjectTable = $table->gdoFileObjectTable();
		return $this;
	}
	
	public function getInitialFiles()
	{
		if ( (!$this->gdo) || (!$this->gdo->isPersisted()) )
		{
			return array();
		}
		return $this->fileTable->select('gdo_file.*')->fetchTable(GDO_File::table())->
			where('files_object='.$this->gdo->getID())->
			joinObject('files_file')->exec()->fetchAllObjects();
	}
	
	/**
	 * @return GDO_File
	 */
	private $files = null;
	public function getValidationValue()
	{
		if (!$this->files)
		{
			$this->files = array_merge($this->getInitialFiles(), Arrays::arrayed($this->getFiles($this->name)));
		}
		return $this->files;
	}
	
	public function gdoAfterCreate()
	{
		$this->gdoAfterUpdate();
	}
	
	public function gdoAfterUpdate()
	{
		if ($files = $this->getValidationValue())
		{
			$this->updateFiles($files);
		}
	}
	
	private function updateFiles($files)
	{
		foreach ($files as $file)
		{
			$this->updateFile($file);
		}
	}
	
	private function updateFile(GDO_File $file)
	{
		if (!$this->fileTable->getBy('files_file', $file->getID()))
		{
			$this->fileTable->blank(array(
				'files_object' => $this->gdo->getID(),
				'files_file' => $file->getID(),
			))->insert();
		}
	}
	
	public function onDeleteFiles(array $ids)
	{
		foreach ($ids as $id)
		{
			if ($file = $this->fileTable->getBy('files_file', $id))
			{
				$file->delete();
			}
		}
	}
	
}
