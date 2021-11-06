<?php
namespace GDO\Mail;

use GDO\Core\Debug;
use GDO\Core\GDT_Template;
use GDO\MailGPG\GDO_PublicKey;
use GDO\User\GDO_User;
use GDO\CLI\CLI;
use GDO\Util\Strings;

/**
 * Will send very simple html and plaintext mails.
 * Supports GPG signing and encryption.
 * Uses UTF8 encoding and features attachments.
 * 
 * @TODO: Implement cc and bcc
 * @TODO: Make use of staff cc?
 * @TODO: Test Attechments in combination with GPG
 * @TODO: Implement GPG Signatures
 * 
 * @author Inferno
 * @author gizmore
 * 
 * @version 6.10.1
 * @since 1.0.0
 * */
final class Mail
{
	public static $SENT = 0; # perf
	public static $DEBUG = GDO_DEBUG_EMAIL;
	public static $ENABLE = GDO_ENABLE_EMAIL;
	
	const HEADER_NEWLINE = "\n";
	const GPG_PASSPHRASE = ''; #GDO_EMAIL_GPG_SIG_PASS;
	const GPG_FINGERPRINT = ''; #GDO_EMAIL_GPG_SIG;

	private $reply = '';
	private $replyName = '';
	private $receiver = '';
	private $receiverName = '';
	private $return = '';
	private $returnName = '';
	private $sender = '';
	private $senderName = '';
	private $subject = '';
	private $body = '';
	private $attachments = [];
	private $headers = [];
	private $gpgKey = '';
	private $resendCheck = false;

	private $allowGPG = true;

	public function setReply($r) { $this->reply = $r; }
	public function setReplyName($rn) { $this->replyName = $rn; }
	public function setSender($s) { $this->sender = $s; }
	public function setSenderName($sn) { $this->senderName = $sn; }
	public function setReturn($r) { $this->return = $r; }
	public function setReturnName($rn) { $this->returnName = $rn; }
	public function setReceiver($r) { $this->receiver = $r; }
	public function setReceiverName($rn) { $this->receiverName = $rn; }
	public function setSubject($s) { $this->subject = $this->escapeHeader($s); }
	public function setBody($b) { $this->body = $b; }
	public function setGPGKey($k) { $this->gpgKey = $k; }
	public function setAllowGPG($bool) { $this->allowGPG = $bool; }
	public function setResendCheck($bool) { $this->resendCheck = $bool; }
	public function addAttachment($title, $data, $mime='application/octet-stream', $encrypted=true) { $this->attachments[$title] = array($data, $mime, $encrypted); }
	public function removeAttachment($title) { unset($this->attachments[$title]); }
	
	private function escapeHeader($h) { return str_replace("\r", '', str_replace("\n", '', $h)); }
	
	public function addAttachmentFile($title, $filename)
	{
		$mime = mime_content_type($filename);
		$data = file_get_contents($filename);
		return $this->addAttachment($title, $data, $mime);
	}
	
	/**
	 * @return self
	 */
	public static function botMail()
	{
		$mail = new self();
		$mail->setSender(GDO_BOT_EMAIL);
		$mail->setSenderName(GDO_BOT_NAME);
		return $mail;
	}
	
	private function getUTF8Reply()
	{
		if ($this->reply === '')
		{
			return $this->getUTF8Sender();
		}
		return $this->getUTF8($this->reply, $this->replyName);
	}
	
	private function getUTF8Return()
	{
		if ($this->reply === '')
		{
			return $this->getUTF8Sender();
		}
		return $this->getUTF8($this->return, $this->returnName);
	}
	
	private function getUTF8($email, $name)
	{
		return $name === '' ? $email : '"'.$this->getUTF8Encoded($name)."\" <{$email}>";
	}
	
	private function getUTF8Sender()
	{
		return $this->getUTF8($this->sender, $this->senderName);
	}
	
	private function getUTF8Receiver()
	{
		return $this->getUTF8($this->receiver, $this->receiverName);
	}

	private function getUTF8Subject() { return $this->getUTF8Encoded($this->subject); }
	
	private function getUTF8Encoded($string) { return '=?UTF-8?B?'.base64_encode($string).'?='; }
	
	public static function sendMailS($sender, $receiver, $subject, $body, $html=false, $resendCheck=false)
	{
		$mail = new self();
		$mail->setSender($sender);
		$mail->setReceiver($receiver);
		$mail->setSubject($subject);
		$mail->setBody($body);
		$mail->setResendCheck($resendCheck);

		return false === $html
			? $mail->sendAsText()
			: $mail->sendAsHTML();
	}

	public static function sendDebugMail($subject, $body)
	{
		$to = defined('GDO_ERROR_EMAIL') ? GDO_ERROR_EMAIL : GDO_ADMIN_EMAIL;
		return self::sendMailS(GDO_BOT_EMAIL, $to, GDO_SITENAME.": ".$subject, Debug::getDebugText($body), false, true);
	}
	
	public function nestedHTMLBody()
	{
		if (self::$DEBUG || (!class_exists('GDO\Core\GDT_Template')))
		{
		    if (class_exists('GDO\Util\Strings'))
		    {
		        return Strings::nl2brHTMLSafe($this->body);
		    }
		    return $this->body;
		}
		$tVars = [
			'content' => $this->body,
		];
		return GDT_Template::php('Mail', 'mail.php', $tVars);
	}

	public function nestedTextBody()
	{
	    return CLI::htmlToCLI($this->body);
	}

	/**
	 * This requires a GDO_User and chooses preferences.
	 * Simply do not call it when you use Mail as standalone.
	 * @param GDO_User $user
	 */
	public function sendToUser(GDO_User $user)
	{
		if ($mail = $user->getMail())
		{
			$this->setReceiver($mail);
			$this->setReceiverName($user->displayNameLabel());
			
			$this->setupGPG($user);
	
			if ($user->wantsTextMail())
			{
				return $this->sendAsText();
			}
			else
			{
				return $this->sendAsHTML();
			}
		}
	}

	public function sendAsText($cc='', $bcc='')
	{
		return $this->send($cc, $bcc, $this->nestedTextBody(), false);
	}

	public function sendAsHTML($cc='', $bcc='')
	{
		return $this->send($cc, $bcc, $this->nestedHTMLBody(), true);
	}

	public function send($cc, $bcc, $message, $html=true)
	{
		self::$SENT++;
		
		if (count($this->attachments) > 0)
		{
			return $this->sendWithAttachments($cc, $bcc);
		}
		
		$headers = '';
		$to = $this->getUTF8Receiver();
		$from = $this->getUTF8Sender();
		$subject = $this->getUTF8Subject();
		$contentType = $html ? 'text/html' : 'text/plain';
		$headers .= 
			"Content-Type: $contentType; charset=utf-8".self::HEADER_NEWLINE
			."MIME-Version: 1.0".self::HEADER_NEWLINE
			."Content-Transfer-Encoding: 8bit".self::HEADER_NEWLINE
			."X-Mailer: PHP".self::HEADER_NEWLINE
			.'From: '.$from.self::HEADER_NEWLINE
			.'Reply-To: '.$this->getUTF8Reply().self::HEADER_NEWLINE
			.'Return-Path: '.$this->getUTF8Return();
		$encrypted = $this->encrypt($message);
		if (self::$DEBUG)
		{
			$printmail = sprintf('<h1>Local EMail to %s:</h1><div>%s<br/>%s</div>', htmlspecialchars($to), htmlspecialchars($this->subject), $message);
			echo $printmail;
			return true;
		}
		elseif (self::$ENABLE && module_enabled('Mail'))
	    {
    	    return mail($to, $subject, $encrypted, $headers);
		}
	}
	
	private function sendWithAttachments($cc, $bcc)
	{
		$to = $this->getUTF8Receiver();
		$from = $this->getUTF8Sender();
		$subject = $this->getUTF8Subject();
		$random_hash = sha1(microtime(true));
		$bound_mix = "GDO6-MIX-{$random_hash}";
		$bound_alt = "GDO6-ALT-{$random_hash}";
		$headers = 
			"Content-Type: multipart/mixed; boundary=\"{$bound_mix}\"".self::HEADER_NEWLINE
			."MIME-Version: 1.0".self::HEADER_NEWLINE
			."Content-Transfer-Encoding: 8bit".self::HEADER_NEWLINE
			."X-Mailer: PHP".self::HEADER_NEWLINE
			.'From: '.$from.self::HEADER_NEWLINE
			.'Reply-To: '.$this->getUTF8Reply().self::HEADER_NEWLINE
			.'Return-Path: '.$this->getUTF8Return();
		
		$message  = "--$bound_mix\n";
		$message .= "Content-Type: multipart/alternative; boundary=\"$bound_alt\"\n";
		$message .= "\n";
		
		$message .= "--$bound_alt\n";
		$message .= "Content-Type: text/plain; charset=utf-8\n";
		$message .= "Content-Transfer-Encoding: 8bit\n";
		$message .= "\n";
		
		$message .= $this->encrypt($this->nestedTextBody());
		$message .= "\n\n";
		
		$message .= "--$bound_alt\n";
		$message .= "Content-Type: text/html; charset=utf-8\n";
		$message .= "Content-Transfer-Encoding: 8bit\n";
		$message .= "\n";
		
		$message .= $this->encrypt($this->nestedHTMLBody());
		$message .= "\n\n";
		
		$message .= "--$bound_alt--\n";
		$message .= "\n";
		
		foreach ($this->attachments as $filename => $attachdata)
		{
			list($attach, $mime, $encrypted) = $attachdata;
			$filename = preg_replace("/[^a-z0-9_\-\.]/i", '', $filename);
			$message .= "--$bound_mix\n";
			$message .= "Content-Type: $mime; name=\"$filename\"\n";
			$message .= "Content-Transfer-Encoding: base64\nContent-Disposition: attachment\n\n";
			if ($encrypted)
			{
				$message .= $this->encrypt(chunk_split(base64_encode($attach)));
			}
			else
			{
				$message .= chunk_split(base64_encode($attach));
			}
		}
		
		$message .= "--$bound_mix--\n\n";
		
		if (self::$DEBUG)
		{
			printf('<h1>Local EMail:</h1><div>%s<br/>%s</div>', htmlspecialchars($this->subject), $message);
			return true;
		}
		elseif (self::$ENABLE && module_enabled('Mail'))
		{
            return mail($to, $subject, $message, $headers);
		}
	}
	
	public function setupGPG(GDO_User $user)
	{
		if ($this->allowGPG)
		{
			if (function_exists('gnupg_init'))
			{
				if (module_enabled('MailGPG'))
				{
					if ($fingerprint = GDO_PublicKey::getFingerprintForUser($user))
					{
						$this->setGPGKey($fingerprint);
					}
				}
			}
		}
	}

	private function encrypt($message)
	{
		if ($this->gpgKey === '' && self::GPG_FINGERPRINT === '')
		{
			return $message;
		}

		if (false === function_exists('gnupg_init'))
		{
			return $message.PHP_EOL.'GnuPG Error: gnupg extension is missing.';
		}

		if (false === ($gpg = gnupg_init()))
		{
			return $message.PHP_EOL.'GnuPG Error: gnupg_init() failed.';
		}

		if ($this->gpgKey !== '')
		{
			if (false === gnupg_addencryptkey($gpg, $this->gpgKey))
			{
				return $message.PHP_EOL.'GnuPG Error: gnupg_addencryptkey() failed.';
			}
		}

		$signed = false;
//		if (self::GPG_FINGERPRINT !== '') {
//			$sign_key = preg_replace('/[^a-z0-9]/i', '', self::GPG_FINGERPRINT);
//
//			if (self::GPG_PASSPHRASE==='')
//			{
//				if (false === gnupg_addsignkey($gpg, $sign_key)) {
//					$message .= PHP_EOL.'GnuPG Error: gnupg_addsignkey1() failed.';
//				}
//				else {
//					$signed = true;
//				}
//			}
//			else
//			{
//				if (false === gnupg_addsignkey($gpg, $sign_key, self::GPG_PASSPHRASE)) {
//					$message .= PHP_EOL.'GnuPG Error: gnupg_addsignkey2() failed.';
//				}
//				else {
//					$signed = true;
//				}
//			}
//
//		}

		if ($signed === true)
		{
			if (false === ($back = gnupg_encryptsign($gpg, $message))) {
				return $message.PHP_EOL.'GnuPG Error: gnupg_encryptsign() failed.';
			}
		}
		else
		{
			if (false === ($back = gnupg_encrypt($gpg, $message))) {
				return #$message.PHP_EOL.
					'GnuPG Error: gnupg_encrypt() failed.'.PHP_EOL.'Message has been removed!';
			}
		}

		return $back;
	}
	
}
