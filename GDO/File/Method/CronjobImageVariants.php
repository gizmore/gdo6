<?php
namespace GDO\File\Method;

use GDO\Core\ModuleLoader;
use GDO\Core\GDO_Module;
use GDO\Core\GDO;
use GDO\File\GDT_File;
use GDO\File\GDT_ImageFile;
use GDO\File\GDT_ImageFiles;
use GDO\File\GDO_File;
use GDO\File\FileUtil;
use GDO\File\ImageResize;
use GDO\Cronjob\MethodCronjob;

/**
 * This cronjob creates missing image variants for GDT_Files.
 * This might be useful when you change the variants for a GDT_Files.
 * Up to date variants are created on every cronjob call.
 * 
 * @author gizmore@wechall.net
 *
 */
final class CronjobImageVariants extends MethodCronjob
{
	private $numFiles = 0;
	private $numVariantFiles = 0;
	private $numConverted = 0;
	private $numErased = 0;
	
	public function run()
	{
		foreach (ModuleLoader::instance()->getModules() as $module)
		{
		    if ($module->isEnabled())
		    {
    			$this->createImageVariantsForModuleClasses($module);
    			$this->createImageVariantsForModuleConfig($module);
		    }
		}
		
		$this->logStatistics();
	}
	
	private function logStatistics()
	{
		$this->logNotice("There are {$this->numFiles} used files in the database in {$this->numVariantFiles} variants.");
		
		if ($this->numConverted)
		{
			$this->logNotice("I just created {$this->numConverted} new variant files.");
		}
		
		if ($this->numErased)
		{
			$this->logError("I had to delete {$this->numErased} files.");
		}
	}
	
	private function createImageVariantsForModuleClasses(GDO_Module $module)
	{
		if ($classes = $module->getClasses())
		{
			foreach ($classes as $table)
			{
				if ($table = GDO::tableFor($table))
				{
					foreach ($table->gdoColumnsCache() as $gdt)
					{
						if ($gdt instanceof GDT_File)
						{
							$this->createImageVariantsFor($table, $gdt);
						}
					}
				}
			}
		}
	}
	
	private function createImageVariantsForModuleConfig(GDO_Module $module)
	{
		if ($config = $module->getConfigCache())
		{
			foreach ($config as $gdt)
			{
				if ($gdt instanceof GDT_ImageFile)
				{
					if ($file = $gdt->getInitialFile())
					{
						$this->createImageVariantsForFile($file, $gdt);
					}
				}
			}
		}
	}
	
	private function createImageVariantsFor(GDO $table, GDT_File $gdt)
	{
		# It's a single file inside a gdo. 
		if ($gdt instanceof GDT_ImageFile)
		{
			$this->createImageVariantsForGDO($table, $gdt);
		}
		# It's a multiple gdt_files relation
		elseif ($gdt instanceof GDT_ImageFiles)
		{
			$this->createImageVariantsForFiles($table, $gdt);
		}
	}
	
	private function createImageVariantsForGDO(GDO $table, GDT_File $gdt)
	{
		# select all gdo's as file
		$query = $table->select($gdt->identifier().'_t.*')-> # from GDO but only joined columns
			where($gdt->identifier() . ' IS NOT NULL')-> # where gdt_file is not null
			fetchTable(GDO_File::table()); # and fetch as file.
		
		$result = $query->exec();
		while ($file = $result->fetchObject())
		{
			$this->createImageVariantsForFile($file, $gdt);
		}
	}
	
	private function createImageVariantsForFiles(GDO $table, GDT_ImageFiles $gdt)
	{
		# Select all files from this gdt filetable.
		$query = $gdt->fileTable->select('gdo_file.*', false)->joinObject('files_file')->fetchTable(GDO_File::table());
		$result = $query->exec();
		while ($file = $result->fetchObject())
		{
			$this->createImageVariantsForFile($file, $gdt);
		}
	}
	
	private function createImageVariantsForFile(GDO_File $file, GDT_File $gdt)
	{
		$this->numFiles++;
		foreach ($gdt->scaledVersions as $name => $dim)
		{
			$this->numVariantFiles++;

			# XXX: UGLY TEMP HACK!
			$file->tempPath($file->getDestPath()); # UGLY!
			# Patched the temp path to real path for image resizer
			
			if (!FileUtil::isFile($file->getVariantPath($name)))
			{
				$this->createImageVariantForFile($file, $gdt, $name, $dim[0], $dim[1]);
			}
		}
	}
	
	/**
	 * @param GDO_File $file
	 * @param GDT_ImageFile $gdt
	 * @param string $name
	 * @param int $width
	 * @param int $height
	 */
	private function createImageVariantForFile(GDO_File $file, GDT_File $gdt, $name, $width, $height)
	{
		if (FileUtil::isFile($file->getDestPath()))
		{
			$dest = $gdt->createFileToScale($file, $name);
			ImageResize::resize($dest, $width, $height);
			$this->numConverted++;
		}
	}
}
