<?php
namespace GDO\Util;

use GDO\File\FileUtil;
use GDO\Core\Module_Core;
use GDO\Javascript\Module_Javascript;

/**
 * Very basic on-the-fly javascript mangler.
 * Changes are detected by md5.
 * You can configure this feature in Module_Core. Make sure you detect the binaries.
 * Output goes to assets/ now instead of temp/ as temp/ is a protected folder.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 4.1.0
 * @see Module_Javascript
 */
final class MinifyJS
{
	# Binary pathes
	private $nodejs;
	private $uglify;
	private $annotate;
	
	private $input;
	private $processedSize = 0;
	private $error = false;

	private $external = array();
	private $concatenate = array();
	
	private $skipMinified = false;
	
	public static function tempDirS($path='') { return GDO_PATH . 'assets/' . $path; }

	public static function minified(array $javascripts)
	{
		$minify = new self($javascripts);
		return $minify->execute();
	}
	
	public function __construct(array $javascripts, $skipMinified=false)
	{
		$this->input = $javascripts;
		$module = Module_Javascript::instance();
		$this->nodejs = $module->cfgNodeJSPath();
		$this->uglify= $module->cfgUglifyPath();
		$this->annotate = $module->cfgAnnotatePath();
		$this->skipMinified = $skipMinified;
		FileUtil::createDir($this->tempDir());
	}
	
	public function tempDir($path='') { return self::tempDirS($path); }
	
	public function finalHash() { return md5(implode('|', array_keys($this->concatenate))); }
	
	public function earlyHash() { return md5(implode('|', $this->input)); }
	
	public function execute()
	{
		# Pass 1 - Early hash
		$earlyhash = $this->earlyhash();
		$earlypath = $this->tempDir("$earlyhash.js");
		if (FileUtil::isFile($earlypath))
		{
			foreach ($this->input as $path)
			{
				if ((strpos($path, '://')) ||
				    (strpos($path, '//') === 0) || 
				    (strpos($path, GWF_WEB_ROOT . 'index.php?') === 0) )
				{
					$this->external[] = $path;
				}
			}
			$this->external[] = "assets/$earlyhash.js?vc=".Module_Core::instance()->cfgAssetVersion();
			return $this->external;
		}
		
		# Pass 2 - Rebuild
		
		# Minify single files and sort them in concatenate and external
		$minified = array_map(array($this, 'minifiedJavascriptPath'), $this->input);
		
		if ($this->error)
		{
			return $this->input;
		}
		
		# Build final file
		$finalhash = $this->finalHash();
		$finalpath = $this->tempDir("$finalhash.js");
		if (!FileUtil::isFile($finalpath))
		{
			$concat = implode(' ', $this->concatenate);
			$cat = 'cat';
			if (Process::isWindows())
			{
			    $cat = 'type';
			    $concat = str_replace('/', '\\', $concat);
			}
			$command = "$cat $concat > $finalpath";
			exec($command);
			if (!(FileUtil::isFile($finalpath)))
			{
				return $minified; # Fail, inbetween version should be ok though.
			}
		}
		
		# Copy to early access
		copy($finalpath, $earlypath);
		
		# Abuse external as small JS.
		$this->external[] = "assets/$finalhash.js?vc=".Module_Core::instance()->cfgAssetVersion();
		return $this->external;
	}
	
	public function minifiedJavascriptPath($path)
	{
		if ( (!strpos($path, '://')) &&
		     (strpos($path, GWF_WEB_ROOT . 'index.php') !== 0) &&
		     (strpos($path, '//' !== 0)) )
		{
			return $this->minifiedJavascript($path);
		}
		else
		{
			$this->external[] = $path;
			return $path;
		}
	}
	
	public function minifiedJavascript($path)
	{
		$src = GDO_PATH . Strings::substrTo($path, '?', $path);
		
		if (FileUtil::isFile($src))
		{
			$this->processedSize += filesize($src);
			$md5 = md5(file_get_contents($src));
			$dest = $this->tempDir("$md5.js");
			if (!FileUtil::isFile($dest))
			{
				if (strpos($src, '.min.js') && $this->skipMinified)
				{
					if (!@copy($src, $dest)) # Skip minified ones
					{
						$this->error = true;
						$this->external[] = $path;
						return $path;
					}
				}
				else
				{
					# Build command
					$annotate = $this->annotate;
					$uglifyjs = $this->uglify;
					$nodejs = $this->nodejs;
					# TODO: remove console.log calls+
					if (Process::isWindows())
					{
					    $command = "$annotate -ar $src | $uglifyjs --compress --mangle -o $dest";
					}
					else
					{
    					$command = "$nodejs $annotate -ar $src | $nodejs $uglifyjs --compress --mangle -o $dest";
					}
					$return = 0;
					$output = array();
					exec($command, $output, $return);
					if ($return != 0)
					{
						$this->error = true;
						$this->external[] = $path;
						return $path; # On error, the original file is left. so you notice.
					}
				}
			}
			$this->concatenate[$md5] = $dest;
			return $dest;
		}
		$this->external[] = $path;
		return $path;
	}
	
}
