<?php
namespace GDO\File\Method;

use GDO\Core\Method;
use GDO\File\GDT_File;
use GDO\File\GDO_File;
use GDO\User\GDO_User;

class Download extends Method
{
    public function gdoParameters()
    {
        return [
            GDT_File::make('id')->notNull(),
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
        $file = $this->getFile();
        
        if ($this->g)
    }
        
    public function getToken(GDO_User $user, GDO_File $file)
    {
        return substr(sha1($user->gdoHashcode() . GDO_SALT . $file->gdoHashcode()), 0, 16);
    }

    
    
}
