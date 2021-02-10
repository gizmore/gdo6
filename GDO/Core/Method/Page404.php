<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;

/**
 * Render a 404 page. Status 404. No saving of last url.
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
    }
    
    public function getTitle()
    {
        return t('err_404');
    }
    
}
