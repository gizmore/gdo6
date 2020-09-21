<?php
namespace GDO\User;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_Name;
use GDO\UI\GDT_EditButton;
use GDO\DB\GDT_UInt;
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
		    GDT_UInt::make('perm_level')->bytes(2),
		);
	}
	
	public static function getByName($name) { return self::getBy('perm_name', $name); }
	
	public static function getOrCreateByName($name, $level=null) { return self::create($name, $level); }
	
	public static function create($name, $level=null)
	{
		if (!($perm = self::getByName($name)))
		{
			$perm = self::blank(['perm_name' => $name, 'perm_level' => $level])->insert();
		}
		elseif ($perm->getLevel() != $level)
		{
		    # Fix level because install method makes sure the permission exists.
		    if ($perm->getLevel() === null)
		    {
    		    $perm->saveVar('perm_level', $level);
		    }
		}
		
		return $perm;
	}
	
	##############
	### Getter ###
	##############
	public function getName() { return $this->getVar('perm_name'); }
	public function getLevel() { return $this->getVar('perm_level'); }
	
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
