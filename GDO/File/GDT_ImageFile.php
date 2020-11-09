<?php
namespace GDO\File;

/**
 * A single file that uses WithImageFile extension trait.
 * 
 * @see GDT_File
 * @see GDT_Files
 * @see GDT_ImageFiles
 * 
 * @license MIT
 * @author gizmore@wechall.net
 * @version 6.10
 * @since 6.00
 */
final class GDT_ImageFile extends GDT_File
{
	use WithImageFile;
	
	public function defaultLabel() { return $this->label('image'); }
	
	public function __construct()
	{
	    parent::__construct();
	    $this->icon = 'image';
	}
	
}
