<?php
namespace GDO\Table;
use GDO\DB\Query;
use GDO\Core\GDT;
use GDO\Util\Math;
use GDO\Core\Application;
use GDO\Core\GDT_Template;
use GDO\UI\WithHREF;
use GDO\UI\WithLabel;

class GDT_PageMenu extends GDT
{
	use WithHREF;
	use WithLabel;
	
	public $numItems = 0;
	public function items($numItems)
	{
		$this->numItems = $numItems;
		return $this;
	}
	
	public $ipp = 10;
	public function ipp($ipp)
	{
		$this->ipp = $ipp;
		return $this;
	}
	
	/**
	 * Set num items via query.
	 * @optional
	 * @param Query $query
	 * @return \GDO\Table\GDT_PageMenu
	 */
	public function query(Query $query)
	{
		$this->query = $query->copy()->select('COUNT(*)');
		$this->numItems = $this->query->exec()->fetchValue();
		return $this;
	}
	private $query = null;
	
	public function getPages()
	{
		return self::getPageCountS($this->numItems, $this->ipp);
	}
	
	public static function getPageCountS($numItems, $ipp)
	{
		return max(array(intval((($numItems-1) / $ipp)+1), 1));
	}
	
	public $shown = 3;
	public function shown($shown)
	{
		$this->shown = $shown;
		return $this;
	}
	
	
	public function filterQuery(Query $query)
	{
		$query->limit($this->ipp, $this->getFrom());
		return $this;
	}
	
	public function getPage()
	{
		return Math::clamp($this->filterValue(), 1, $this->getPages());
	}
	
	public function getFrom()
	{
		return self::getFromS($this->getPage(), $this->ipp);
	}
	
	public static function getFromS($page, $ipp)
	{
		return ($page - 1) * $ipp;
	}
	
	##############
	### Render ###
	##############
	public function initJSON()
	{
		return $this->renderJSON();
	}
	
	public function renderCell()
	{
		switch (Application::instance()->getFormat())
		{
			case 'json': return $this->renderJSON();
			case 'html': default: return $this->renderHTML();
		}
	}
	
	public function renderJSON()
	{
		return array(
			'href' => $this->href,
			'items' => $this->numItems,
			'ipp' => $this->ipp,
			'page' => $this->getPage(),
			'pages' => $this->getPages(),
		);
	}
	
	public function renderHTML()
	{
		if ($this->getPages() > 1)
		{
			$tVars = array(
				'pagemenu' => $this,
				'pages' => $this->pagesObject(),
			);
			return GDT_Template::php('Table', 'cell/pagemenu.php', $tVars);
		}
	}
	
	private function replaceHREF($page)
	{
		$this->href = preg_replace("#&f\\[{$this->name}\\]=\\d+#", '', $this->href);
		return $this->href . '&f[' . $this->name . ']='. $page;
	}
	
	private function pagesObject()
	{
		$curr = $this->getPage();
		$nPages = $this->getPages();
		$pages = [];
		$pages[] = new PageMenuItem($curr, $this->replaceHREF($curr), true);
		for ($i = 1; $i <= $this->shown; $i++)
		{
			$page = $curr- $i;
			if ($page > 0)
			{
				array_unshift($pages, new PageMenuItem($page, $this->replaceHREF($page)));
			}
			$page = $curr+ $i;
			if ($page <= $nPages)
			{
				$pages[] = new PageMenuItem($page, $this->replaceHREF($page));
			}
		}
		
		if (($curr - $this->shown) > 1)
		{
			array_unshift($pages, PageMenuItem::dotted());
			array_unshift($pages, new PageMenuItem(1, $this->replaceHREF(1)));
		}

		if (($curr + $this->shown) < $nPages)
		{
			$pages[] = PageMenuItem::dotted();
			$pages[] = new PageMenuItem($nPages, $this->replaceHREF($nPages));
		}
		
		return $pages;
	}
}
