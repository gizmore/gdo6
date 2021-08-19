<?php
namespace GDO\UI;

/**
 * Add text variable to a GDT.
 * Evaluated lazy.
 *
 * @author gizmore
 * @version 6.10.6
 * @since 6.4.0
 */
trait WithText
{
	# Raw
	private $textRaw = null;
	public function textRaw($text)
	{
		$this->textRaw = $text;
		return $this;
	}

	# I18n
	private $textKey = null;
	private $textArgs = null;
	public function text($key, array $args = null)
	{
		$this->textKey = $key;
		$this->textArgs = $args;
		return $this;
	}

	##############
	### Render ###
	##############
	public function hasText()
	{
		return $this->textKey || $this->textRaw;
	}

	public function renderText()
	{
		return $this->textRaw ?
			$this->textRaw :
			t($this->textKey, $this->textArgs);
	}

}
