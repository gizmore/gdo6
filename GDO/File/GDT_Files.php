<?php
namespace GDO\File;

use GDO\Util\Arrays;
use GDO\User\GDO_User;
use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Success;

/**
 * Use this GDT in a has_many files relationship.
 * You have to create and specify a file table that is M:N for your GDO and the GDO_File entry.
 * Upload is handled by inheritance of GDT_File.
 * 
 * @see GDT_File
 * @see GDO_FileTable
 * 
 * @author gizmore@wechall.net
 * @version 6.11.0
 * @since 6.8.0
 */
class GDT_Files extends GDT_File
{
	public function defaultLabel() { return $this->label('files'); }
	
	########################
	### STUB GDT methods ###
	########################
	public $autojoin = false;
	public function gdoColumnNames() {} # NO DB column.
	public function gdoColumnDefine() { return null; } # NO DB column. Your GDO_FileTable has the data.
	public function getGDOData() {} # Only relation table. Handled by onCreate and onUpdate.
	public function setGDOData(GDO $gdo=null) { return $this; }
	
	/**
	 * @var $value GDO_File[]
	 */
	public function toVar($value) {} # cannot be saved as column.
	
	##################
	### File Table ###
	##################
	/** @var $fileTable GDO **/
	public $fileTable;
	/** @var $fileObjectTable GDO **/
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
	public $multiple = true;
	
	public function getInitialFiles()
	{
		if ( (!$this->gdo) || (!$this->gdo->isPersisted()) )
		{
			return []; # has no stored files as its not even saved yet.
		}
		# Fetch all from relation table as GDO_File array.
		return $this->fileTable->select('*, files_file_t.*')->
			fetchTable(GDO_File::table())->
			where('files_object='.$this->gdo->getID())->
			exec()->fetchAllObjects();
	}
	
	/**
	 * @return GDO_File[]
	 */
	public function getValidationValue()
	{
		if (empty($this->files))
		{
			$this->files = array_merge(
				$this->getInitialFiles(),
				Arrays::arrayed(
					$this->getFiles($this->name)));
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
		$this->files = [];
	}
	
	private function updateFiles(array $files)
	{
		foreach ($files as $file)
		{
			$this->updateFile($file);
		}
	}
	
	/**
	 * Update relation table if
	 * 1. File is persisted
	 * 2. Not in relation table yet.
	 * @param GDO_File $file
	 */
	private function updateFile(GDO_File $file)
	{
	    if ($this->gdo)
	    {
    		if ($file->isPersisted())
    		{
    			if (!$this->fileTable->getBy('files_file', $file->getID()))
    			{
    				# Insert in relation table for GDT_Files
    				$this->fileTable->blank(array(
    					'files_object' => $this->gdo->getID(),
    					'files_file' => $file->getID(),
    				))->insert();
    			}
    		}
	    }
	}
	
	/**
	 * This is the delete action that removes the files.
	 * 
	 */
	public function onDeleteFiles(array $ids)
	{
		foreach ($ids as $id)
		{
			if ($file = $this->fileTable->getBy('files_file', $id))
			{
				if ($file->canDelete(GDO_User::current()))
				{
					$file = $file->getFile();
					$file->delete();
					GDT_Response::make()->addField(GDT_Success::make()->text('msg_file_deleted'));
				}
			}
		}
	}
	
}
