<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;

trait WithOrder
{
	public $orderable = true;
	public function orderable($orderable=true) { $this->orderable = $orderable; return $this; }
	public function displayTableOrder(GDT_Table $table)
	{
		if ($this->orderable)
		{
			$name = $this->name;
			$o = $table->headers->name . "[$name]";
			
			$url = $_SERVER['REQUEST_URI'];
			
			# Check if one arrow is selected
			$is_asc = strpos($url, "&$o=1") !== false;
			$is_desc = strpos($url, "&$o=0") !== false;
			
			$is_asc = $is_asc || isset($_REQUEST['o'][$name]) && $_REQUEST['o'][$name];
			$is_desc = $is_desc || isset($_REQUEST['o'][$name]) && (!$_REQUEST['o'][$name]);
			
			# Clean url of my own ordering
			$url = str_replace("&$o=0", '', $url);
			$url = str_replace("&$o=1", '', $url);
			
			# Clean url of paging
			if ($pagemenu = $table->getPageMenu())
			{
				$url = preg_replace("#&f\\[{$pagemenu->name}\\]=\\d+#", '', $url);
			}
			
			# Arrow urls
			$url_asc = $is_asc ? $url : $url . "&$o=1";
			$url_desc = $is_desc ? $url : $url . "&$o=0";
			
			# Template
			$tVars = array(
				'field' => $this,
				'is_asc' => $is_asc,
				'is_desc' => $is_desc,
				'url_asc' => $url_asc,
				'url_desc' => $url_desc,
			);
			return GDT_Template::php('Table', 'filter/_table_order.php', $tVars);
		}
	}
	
}