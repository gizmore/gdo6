<?php
namespace GDO\File;

use GDO\Util\Arrays;

/**
 * Use this GDT in a has_many files relationship.
 * You have to create and specify a file table that is M:N for your GDO and the GDO_File entry.
 * Upload is handled by inheritance of GDT_File.
 * 
 * @see GDT_File
 * @see GDO_FileTable
 * 
 * @author gizmore@wechall.net
 * 
 * @since 6.08
 * @version 6.08
 */
class GDT_Files extends GDT_File
{
	########################
	### STUB GDT methods ###
	########################
	public function gdoColumnDefine() { return null; } # NO DB column. Your GDO_FileTable has the data.
	public function getGDOData() { return null; } # Only relation table. Handled by onCreate and onUpdate.
	public function toVar($value) { return null; } # cannot be saved as column.
	public function isSerializable() { return false; } # cannot be transmitted or serialized.
	
	##################
	### File Table ###
	##################
	public $fileTable;
	public $fileObjectTable;
	public function fileTable(GDO_FileTable $table)
	{
		$this->fileTable = $table;
		$this->fileObjectTable = $table->gdoFileObjectTable();
		return $this;
	}
	
	#########################
	### GDT_File override ###
	#########################
	public function getInitialFiles()
	{
		if ( (!$this->gdo) || (!$this->gdo->isPersisted()) )
		{
			return array(); # has no stored files as its not even saved yet.
		}
		# Fetch all from relation table as GDO_File array.
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
	
	#############
	### Hooks ###
	#############
	/**
	 * After creation and update we have to create the entry in the relation table.
	 */
	public function gdoAfterCreate()
	{
		$this->gdoAfterUpdate();
	}
	
	/**
	 * After creation and update we have to create the entry in the relation table.
	 */
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
	
	/**
	 * This is the delete action that removes the files.
	 */
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
