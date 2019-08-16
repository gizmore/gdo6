<?php
namespace GDO\User;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_Name;
use GDO\UI\GDT_EditButton;
/**
 * @author gizmore
 */
final class GDO_Permission extends GDO
{
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('perm_id'),
			GDT_Name::make('perm_name')->unique(),	
		);
	}
	
	public static function getByName($name) { return self::getBy('perm_name', $name); }
	
	public static function getOrCreateByName($name) { return self::create($name); }
	
	public static function create($name)
	{
		if (!($perm = self::getByName($name)))
		{
			$perm = self::blank(['perm_name'=>$name])->insert();
		}
		return $perm;
	}
	
	##############
	### Getter ###
	##############
	public function getName() { return $this->getVar('perm_name'); }
	
	###############
	### Display ###
	###############
	public function displayName() { return t('perm_'.$this->getName()); }
	public function display_perm_edit() { return GDT_EditButton::make()->href($this->hrefEdit()); }
	public function display_user_count() { return $this->getVar('user_count'); }
	public function renderChoice() { return sprintf('%s–%s', $this->getID(), $this->displayName()); }
	
	############
	### HREF ###
	############
	public function href_btn_edit() { return href('Admin', 'ViewPermission', '&permission='.$this->getID()); }
	
	
}
