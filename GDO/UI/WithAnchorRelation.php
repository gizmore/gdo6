<?php
namespace GDO\UI;

/**
 * Adds anchor relation to a GDT.
 *
 * @see GDT_Link
 * @see GDT_Button
 * @see GDT_IconButton
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
trait WithAnchorRelation
{
	#####################
	### Link relation ###
	#####################
    private $relation = '';
	public function noFollow() { return $this->relation(GDT_Link::REL_NOFOLLOW); }
	public function getRelation() { return $this->relation; }
	public function htmlRelation() { $rel = $this->getRelation(); return $rel ? " rel=\"$rel\"" : ''; }
	public function relation($relation)
	{
		$this->relation = $relation ? trim($this->relation . " $relation") : $this->relation;
		return $this;
	}

}
