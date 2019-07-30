<?php
namespace GDO\Net;

use GDO\Form\GDT_Select;

final class GDT_Protocol extends GDT_Select
{
	const HTTP = 'http';
	const HTTPS = 'https';
	const SSH = 'ssh';
	const IRC = 'irc';
	const IRCS = 'ircs';
	const FTP = 'ftp';
	const FTPS = 'ftps';
	const SFTP = 'sftp';
	const TCP = 'tcp';
	const TCPS = 'tcps';
	const RDP = 'rdp';
	
	###########
	### GDT ###
	###########
	public function allowHTTP()
	{
		return $this->allowProtocols('http', 'https');
	}
	
	public $protocols = [];
	public function allowProtocols(string ...$protocols)
	{
		$this->protocols = array_unique(array_merge($this->protocols, $protocols));
		$this->protocols = array_combine($this->protocols, $this->protocols);
		return $this;
	}
	
	public function allowProtocol(string $protocol, bool $allow=true)
	{
		if ($allow)
		{
			$this->protocols[$protocol] = $protocol;
		}
		else
		{
			unset($this->protocols[$protocol]);
		}
		return $this;
	}
	
	####################
	### Init Choices ###
	####################
	public function initChoices()
	{
		if (!$this->choices)
		{
			$choices = array();
			if ($this->emptyValue)
			{
				$choices = array($this->emptyValue => $this->emptyLabel);
			}
			$choices = array_merge($choices, $this->protocols);
			return $this->choices($choices);
		}
		return $this->choices;
	}
	
	##############
	### Render ###
	##############
	public function render()
	{
		$this->initChoices();
		return parent::render();
	}
	
	################
	### Validate ###
	################
	public function validate($value)
	{
		$this->initChoices();
		return parent::validate($value);
	}

}
