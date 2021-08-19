<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\DB\GDT_Name;
use GDO\DB\GDT_Text;
use GDO\DB\GDT_Index;

/**
 * User settings for larger blob values, e.g. PMSignature.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
final class GDO_UserSettingBlob extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoCached() { return false; }
	public function gdoDependencies() { return ['GDO\User\GDO_User', 'GDO\Core\GDO_Module']; }
	public function gdoColumns()
	{
		return array(
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->primary()->unique(false),
			GDT_Text::make('uset_value')->max(65535),
		    GDT_Index::make('uset_user_index')->indexColumns('uset_user')->hash(),
		);
	}

}
