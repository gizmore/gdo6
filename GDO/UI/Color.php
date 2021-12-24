<?php
namespace GDO\UI;

/**
 * Color utility and conversion object.
 * 
 * @license Stolen from?...
 * 
 * @version 6.11.2
 * @since 6.5.0
 */
final class Color
{
	###############
	### Utility ###
	###############
	public static function fromHex($hex)
	{
		$matches = null;
		if (preg_match("/^#?([a-f0-9]{1,2})([a-f0-9]{1,2})([a-f0-9]{1,2})$/iD", $hex, $matches))
		{
			return new self(hexdec($matches[1]), hexdec($matches[2]), hexdec($matches[3]));
		}
	}
	
	public static function fromHSV($h, $s, $v)
	{
		list($r, $g, $b) = self::hsvToRGB($h, $s, $v);
		return new self($r, $g, $b);
	}

	public static function hsvToRGB($h, $s, $v)
	{
		if ($s === 0)
		{
			$r = $g = $b = round($v * 2.55);
		}
		else
		{
			$h /= 60.0; $s /= 100.0; $v /= 100.0;
			$i = floor($h);
			$f = $h - $i;
			$p = $v * (1-$s);
			$q = $v * (1-$s*$f);
			$t = $v * (1-$s*(1-$f));
			switch($i) {
				case 0: $r=$v; $g=$t; $b=$p; break;
				case 1: $r=$q; $g=$v; $b=$p; break;
				case 2: $r=$p; $g=$v; $b=$t; break;
				case 3: $r=$p; $g=$q; $b=$v; break;
				case 4: $r=$t; $g=$p; $b=$v; break;
				default:$r=$v; $g=$p; $b=$q; break;
			}
			$r = round($r*255); $g = round($g*255); $b = round($b*255);
		}
		return [$r, $g, $b];
	}
	
	##############
	### Object ###
	##############
	private $r, $g, $b;
	
	/**
	 * Colors are 0 - 255.
	 * 
	 * @param int $r 
	 * @param int $g
	 * @param int $b
	 */
	public function __construct($r, $g, $b)
	{
		$this->r = $r; $this->g = $g; $this->b = $b;
	}

	public function asRGB() { return [$this->r, $this->g, $this->b]; }
	
	public function asHex() { return sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b); }
	
	public function asHSV()
	{
		$h = $s = $v = 0;
		$r = $this->r; $g = $this->g; $b = $this->b;
		$max = $this->max3($r, $g, $b);
		$dif = floatval($max - $this->min3($r, $g, $b));
		$s = $max === 0 ? 0 : (100*$dif/$max);
		if ($s === 0) $h = 0;
		elseif ($dif === 0.0) $h = 360; # FIXME: this fixes a crash but all is same color :(
		elseif ($r === $max) $h = 60.0 * ($g - $b) / $dif;
		elseif ($g === $max) $h = 120.0 + 60.0 * ($b - $r) / $dif;
		elseif ($b === $max) $h = 240.0 + 60.0 * ($r - $g) / $dif;
		if ($h < 0) $h += 360;
		$v = round($max*100/255.0);
		$h = round($h);
		$s = round($s);
		return [$h, $s, $v];
	}
	
	public function complementary()
	{
	 	if ( ($this->r == 0) && ($this->g == 0) && ($this->b == 0) )
	 	{
	 		return self::fromHex("#ffffff");
	 	}
		list($h, $s, $v) = $this->asHSV();
		return self::fromHSV($this->hueShift($h, 180), $s, $v);
	}
	
	private function min3($a,$b,$c) { return ($a<$b)?(($a<$c)?$a:$c):(($b<$c)?$b:$c); }
	private function max3($a,$b,$c) { return ($a>$b)?(($a>$c)?$a:$c):(($b>$c)?$b:$c); }
	private function hueShift($h,$s) { $h += $s; while ($h>=360.0) $h-=360.0; while ($h<0.0) $h+=360.0; return $h; }

}
