<?php
namespace GDO\File;

use GDO\Core\GDOError;

/**
 * Utility that resizes images.
 * 
 * @see http://salman-w.blogspot.de/2009/04/crop-to-fit-image-using-aspphp.html
 * @see https://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation
 * 
 * @TODO Use imagemagick and a system/exec call. PHP needs too much mem.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 3.0.0
 */
final class ImageResize
{
	public static function resize(GDO_File $file, $toWidth, $toHeight, $toFormat=null)
	{
		// Gather metadata
		list($source_width, $source_height) = getimagesize($file->path);
		$rotation = self::orientation($file);
		$toFormat = $toFormat == null ? $file->getType() : $toFormat;
		if ( ($rotation == 8) || ($rotation == 6) )
		{
			// Rotate metadata by 90 degree
			$t = $source_width;
			$source_width = $source_height;
			$source_height = $t;
		}
		
		// No change keep file
		if ( ($source_width == $toWidth) && 
			 ($source_height == $toHeight) &&
			 ($file->getType() == $toFormat) )
		{
			return true;
		}
		
		$source = self::getGDImage($file);
		
		// Rotate image if desired
		$source2 = null;
		switch ($rotation)
		{
		case 8: $source2 = imagerotate($source, 90, 0); break;
		case 6: $source2 = imagerotate($source, -90, 0); break;
		case 3: $source2 = imagerotate($source, 180, 0); break;
		}
		if ($source2)
		{
			imagedestroy($source);
			$source = $source2;
		}
		
		// Crop and resize
		if ( ($source_width != $toWidth) ||
			 ($source_height != $toHeight) )
		{
			// Calc aspect ratio
			$source_aspect_ratio = $source_width / $source_height;
			$desired_aspect_ratio = $toWidth / $toHeight;
			
			if ($source_aspect_ratio > $desired_aspect_ratio)
			{
				$temp_height = $toHeight;
				$temp_width = (int) ($toHeight * $source_aspect_ratio);
			}
			else
			{
				$temp_width = $toWidth;
				$temp_height = (int) ($toWidth / $source_aspect_ratio);
			}
			
			/*
			 * Resize the image into a temporary GD image
			 */
			$temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
			imagecopyresampled(
				$temp_gdim,
				$source,
				0, 0,
				0, 0,
				$temp_width, $temp_height,
				$source_width, $source_height
				);
			
			imagedestroy($source);
			
			/*
			 * Copy cropped region from temporary image into the desired GD image
			 */
			$x0 = ($temp_width - $toWidth) / 2;
			$y0 = ($temp_height - $toHeight) / 2;
			$desired_gdim = imagecreatetruecolor($toWidth, $toHeight);
			imagecopy(
				$desired_gdim,
				$temp_gdim,
				0, 0,
				$x0, $y0,
				$toWidth, $toHeight
				);
			imagedestroy($temp_gdim);
		}
		else
		{
			$desired_gdim = $source;
		}
		
		// Detect format change
		$file->setVar('file_type', $toFormat);
		switch ($toFormat)
		{
// 			case "image/bmp": imagewbmp($desired_gdim, $file->path); break;
			case "image/gif": imagegif($desired_gdim, $file->path); break;
			case "image/jpeg": imagejpeg($desired_gdim, $file->path); break;
			case "image/png": imagepng($desired_gdim, $file->path); break;
			default: throw new GDOError('err_image_format_not_supported', [$toFormat]);
		}
		imagedestroy($desired_gdim);
		return true;
	}
	
	private static function orientation(GDO_File $file)
	{
		if (!function_exists('exif_read_data'))
		{
			return -1;
		}
		try
		{
			$exif = @exif_read_data($file->path);
			return (int)(@$exif['Orientation']);
		}
		catch (\Exception $e)
		{
			return -2;
		}
		return 0;
	}
	
	public static function getGDImage(GDO_File $file)
	{
		switch ($file->getType())
		{
// 			case "image/bmp": $source = ImageFromBMP::load($file->path); break;
			case "image/gif": $source = imagecreatefromgif($file->path); break;
			case "image/jpeg": $source = imagecreatefromjpeg($file->path); break;
			case "image/png": $source = imagecreatefrompng($file->path); break;
			default: throw new GDOError('err_image_format_not_supported', [$file->getType()]);
		}
		return $source;
	}
	
	public static function derotate(GDO_File $file)
	{
		$rotation = self::orientation($file);
		
		switch ($rotation)
		{
			case 8: $rotate = 90; break;
			case 6: $rotate = -90; break;
			case 3: $rotate = 180; break;
			default: return $file;
		}
		
		$image = self::getGDImage($file);
		
		$image2 = imagerotate($image, $rotate, 0);
		
		imagedestroy($image);
		
		switch ($file->getType())
		{
// 			case "image/bmp": imagewbmp($desired_gdim, $file->path); break;
			case "image/gif": imagegif($image2, $file->path); break;
			case "image/jpeg": imagejpeg($image2, $file->path); break;
			case "image/png": imagepng($image2, $file->path); break;
			default: throw new GDOError('err_image_format_not_supported', [$file->getType()]);
		}
		
		imagedestroy($image2);
		
		return $file;
	}
}
