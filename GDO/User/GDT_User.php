<?php
namespace GDO\User;
use GDO\DB\GDT_Object;
class GDT_User extends GDT_Object
{
    public function defaultLabel() { return $this->label('user'); }
    
    public function __construct()
    {
        $this->table(GDO_User::table());
        $this->withCompletion();
        $this->icon('face');
    }

    public function withCompletion()
    {
        return $this->completionHref(href('User', 'Completion'));
    }

    private $ghost = false;
    public function ghost($ghost=true)
    {
        $this->ghost = $ghost;
        return $this;
    }
    
    /**
     * @return GDO_User
     */
    public function getUser()
    {
        if (!($user = $this->getValue()))
        {
            if ($this->ghost)
            {
                $user = GDO_User::ghost();
            }
        }
        return $user;
    }
    
    public function renderCell()
    {
        if ($user = $this->getUser())
        {
            return $user->displayName();
        }
        return t('unknown');
    }

    
}
