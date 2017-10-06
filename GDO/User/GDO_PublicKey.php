<?php
namespace GDO\User;
use GDO\Core\GDO;
use GDO\UI\GDT_Message;
use GDO\Core\Logger;

/**
 * Cryptographic public keys for users.
 * @author gizmore
 * @since 4.0
 */
final class GDO_PublicKey extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDT_User::make('gpg_uid')->primary(),
			GDT_Message::make('gpg_pubkey')->caseS()->ascii()->max(16384),
		);
	}

	##############
	### Static ###
	##############
	public static function removeKey($userid) { return self::table()->deleteWhere('gpg_uid='.(int)$userid); }
	public static function updateKey($userid, $file_content) { return self::blank(['gpg_uid'=>$userid, 'gpg_pubkey'=>$file_content])->replace(); }
	public static function getKeyForUser(GDO_User $user) { return self::getKeyForUID($user->getID()); }
	public static function getKeyForUID($userid=null) { return self::table()->select('gpg_pubkey')->where('gpg_uid='.(int)$userid)->exec()->fetchValue(); }
	public static function getFingerprintForUser(GDO_User $user) { return self::getFingerprintForUID($user->getID()); }
	public static function getFingerprintForUID($userid)
	{
		if ($key = self::getKeyForUID($userid))
		{
			return self::grabFingerprint($key);
		}
	}
	
	/**
	 * Return a public key in hex format or false.
	 * @param string $key
	 */
	public static function grabFingerprint($file_content)
	{
		$gpg = gnupg_init();
		if (false === ($result = gnupg_import($gpg, $file_content))) {
			Logger::logCritical('gnupg_import() failed');
			return false;
		}
		if ( ($result['imported']+$result['unchanged']) === 0 ) {
			return false;
		}
		return $result['fingerprint'];
	}
}