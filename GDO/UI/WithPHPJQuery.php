<?php
namespace GDO\UI;

/**
 * Should be extended to reflect a jQuery API on php objects.
 * 
 * @author gizmore
 * @since 6.07
 */
trait WithPHPJQuery
{
	#######################
	### HTML Attributes ###
	#######################
	public $htmlAttributes;
	public function attr($attribute, $value=null)
	{
		if (!$this->htmlAttributes)
		{
			$this->htmlAttributes = [];
		}
		if ($value === null)
		{
			return @$this->htmlAttributes[$attribute];
		}
		$this->htmlAttributes[$attribute] = $value;
		return $this;
	}
	
	public function htmlAttributes()
	{
		$html = '';
		if ($this->htmlAttributes)
		{
			foreach ($this->htmlAttributes as $attribute => $value)
			{
				$html .= " $attribute=\"$value\"";
			}
		}
		return $html;
	}

	public function addClass($class)
	{
		# Old classes
		$classes = explode(" ", $this->attr('class'));
		if (!$classes)
		{
			$classes = [];
		}
		
		# Merge new classes
		$newclss = explode(" ", $class); # multiple possible
		foreach ($newclss as $class)
		{
			if (!in_array($class, $classes, true))
			{
				$classes[] = $class;
				$this->attr('class', implode(" ", $classes));
			}
		}
		
		return $this;
	}
	
	# CSS
	private $css;
	public function css($attr, $value)
	{
		if (!$this->css) $this->css = [];
		$this->css[$attr] = $value;
		return $this->updateCSS();
	}
	
	private function updateCSS()
	{
		$rules = '';
		foreach ($this->css as $key => $value)
		{
			$rules .= "$key: $value;";
		}
		return $this->attr('style', $rules);
	}

}
