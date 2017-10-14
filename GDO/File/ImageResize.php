<?php
namespace GDO\File;
use GDO\Core\GDOError;

/**
 * @see http://salman-w.blogspot.de/2009/04/crop-to-fit-image-using-aspphp.html
 */
final class ImageResize
{
	public static function resize(GDO_File $file, $toWidth, $toHeight, $toFormat=null)
	{
		list($source_width, $source_height, $source_type) = getimagesize($file->path);
		switch ($file->getType())
		{
// 			case "image/bmp": $source = ImageFromBMP::load($file->path); break;
			case "image/gif": $source = imagecreatefromgif($file->path); break;
			case "image/jpeg": $source = imagecreatefromjpeg($file->path); break;
			case "image/png": $source = imagecreatefrompng($file->path); break;
			default: throw new GDOError('err_image_format_not_supported', [$file->getType()]);
		}
	
		$source_aspect_ratio = $source_width / $source_height;
		$desired_aspect_ratio = $toWidth / $toHeight;
		
		if ($source_aspect_ratio > $desired_aspect_ratio) {
			$temp_height = $toHeight;
			$temp_width = (int) ($toHeight * $source_aspect_ratio);
		} else {
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
		
		/*
		 * Convert and save image.
		 */
		$toFormat = $toFormat === null ? $file->getType() : $toFormat;
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
		
	}
	
}
