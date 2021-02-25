<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\Module_Core;
use GDO\Mail\Mail;
use GDO\User\GDO_User;
use GDO\Net\GDT_IP;
use GDO\Core\Website;

/**
 * Render a 404 page.
 * Set HTTP Status to 404.
 * Disable saving of last url.
 * Send 404 mails optionally.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class Page404 extends MethodPage
{
    public function saveLastUrl() { return false; }
    
    public function beforeExecute()
    {
        http_response_code(404);
        if (Module_Core::instance()->cfgMail404())
        {
            $this->send404Mails();
        }
    }
    
    public function getTitle()
    {
        return t('err_404');
    }
    
    public function send404Mails()
    {
        foreach (GDO_User::admins() as $user)
        {
            $this->send404Mail($user);
        }
    }
    
    public function send404Mail(GDO_User $user)
    {
        $mail = Mail::botMail();
        $mail->setSubject(tusr($user, 'mail_subj_404', [sitename()]));
        $mail->setBody($this->send404MailBody($user));
        $mail->sendToUser($user);
    }
    
    private function send404MailBody(GDO_User $user)
    {
        $args = [
            $user->displayNameLabel(),
            sitename(),
            GDT_IP::current(),
            GDO_User::current()->displayNameLabel(),
            html($_SERVER['REQUEST_URI']),
            html(Website::hrefBack()),
        ];
        return tusr($user, 'mail_body_404', $args);
    }
    
}