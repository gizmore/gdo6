<?php
namespace GDO\Table;
use GDO\DB\Query;
use GDO\Core\GDT;
use GDO\Util\Math;
use GDO\Core\Application;
use GDO\Core\GDT_Template;
use GDO\UI\WithHREF;
use GDO\UI\WithLabel;
use GDO\UI\GDT_Link;

class GDT_PageMenu extends GDT
{
	use WithHREF;
	use WithLabel;
	
	public $orderable = false;
	public $searchable = false;
	
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
	 * @return self
	 */
	public function query(Query $query)
	{
		$this->query = $query->copy()->selectOnly('COUNT(*)');
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
	
	/**
	 * @return int
	 */
	public function getPage()
	{
		return (int) Math::clamp($this->filterValue(), 1, $this->getPages());
	}
	
	public function getFrom()
	{
		return self::getFromS($this->getPage(), $this->ipp);
	}
	
	public static function getFromS($page, $ipp)
	{
		return ($page - 1) * $ipp;
	}
	
	public function indexToPage($index)
	{
		return self::indexToPageS($index, $this->ipp);
	}
	
	public static function indexToPageS($index, $ipp)
	{
		return intval($index / $ipp) + 1;
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
		$this->href = preg_replace("#[&?]f\\[{$this->name}\\]=\\d+#", '', $this->href);
		if (!strpos($this->href, '?'))
		{
			return $this->href . '?f[' . $this->name . ']='. $page;
		}
		else
		{
			return $this->href . '&f[' . $this->name . ']='. $page;
		}
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
	
	/**
	 * Get anchor relation for a page. Either next, prev or nofollow.
	 * @see GDT_Link
	 * @param PageMenuItem $page
	 * @return string
	 */
	public function relationForPage(PageMenuItem $page)
	{
		$current = $this->getPage();
		if ( ($page->page - 1) == $current)
		{
			return GDT_Link::REL_NEXT;
		}
		elseif ( ($page->page + 1) == $current)
		{
			return GDT_Link::REL_PREV;
		}
		else
		{
			return GDT_Link::REL_NOFOLLOW;
		}
	}

}
