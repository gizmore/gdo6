<?php
namespace GDO\File\Method;

use GDO\Core\GDT_Secret;
use GDO\Core\Method;
use GDO\File\GDT_File;
use GDO\File\GDO_File;
use GDO\User\GDO_User;
use GDO\DB\GDT_String;

class Download extends Method
{
    public function gdoParameters()
    {
        return [
            GDT_File::make('id')->notNull(),
            GDT_String::make('variant')->initial(''),
            GDT_Secret::make('token')->notNull(),
        ];
    }
    
    /**
     * @return GDO_File
     */
    public function getFile()
    {
        return $this->gdoParameterValue('id');
    }
    
    public function execute()
    {
        $user = GDO_User::current();
        $file = $this->getFile();
        $token = $this->gdoParameterVar('token');
        if ($token !== $this->getToken($user, $file))
        {
            return $this->error('err_token');
        }
        
        $variant = $this->gdoParameterVar('variant');
        GetFile::make()->executeWithId($file->getID(), $variant);
    }
        
    public function getToken(GDO_User $user, GDO_File $file)
    {
        return substr(sha1($user->gdoHashcode() . GDO_SALT . $file->gdoHashcode()), 0, 16);
    }

    
    
}
