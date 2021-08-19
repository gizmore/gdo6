<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\Module_Core;
use GDO\Mail\Mail;
use GDO\User\GDO_User;
use GDO\Net\GDT_IP;
use GDO\Core\Website;
use GDO\Core\GDT_Response;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Link;

/**
 * Render a 403 page.
 * Set HTTP Status to 403.
 * Disable saving of last url.
 * Send 403 mails optionally.
 *
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.6
 */
final class Page403 extends MethodPage
{
    public function isCLI() { return false; }
    public function saveLastUrl() { return false; }
    public function showInSitemap() { return false; }
    public function isTrivial() { return false; } # no auto test

    public function beforeExecute()
    {
        GDT_Response::$CODE = 403;
        http_response_code(403);
        if (Module_Core::instance()->cfgMail403())
        {
            $this->send403Mails();
        }
    }

    public function getTitle()
    {
        return t('err_403');
    }

    public function send403Mails()
    {
        foreach (GDO_User::admins() as $user)
        {
            $this->send403Mail($user);
        }
    }

    public function send403Mail(GDO_User $user)
    {
        $mail = Mail::botMail();
        $mail->setSubject(tusr($user, 'mail_subj_403', [sitename()]));
        $mail->setBody($this->send403MailBody($user));
        $mail->sendToUser($user);
    }

    private function send403MailBody(GDO_User $user)
    {
        $args = [
            $user->displayNameLabel(),
            sitename(),
            GDT_IP::current(),
            GDO_User::current()->displayNameLabel(),
            GDT_Link::make()->href(GDT_Url::absolute($_SERVER['REQUEST_URI']))->render(),
            GDT_Link::make()->href(GDT_Url::absolute(Website::hrefBack()))->render(),
        ];
        return tusr($user, 'mail_body_403', $args);
    }

}
