<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * This GDT renders it's GDO as card.
 *  
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class GDT_CardView extends GDT
{
	public function renderCell() { return $this->gdo->renderCard(); }

}
