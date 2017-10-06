<?php
namespace GDO\Util;
final class HTAccess
{
    public static function protectFolder($path)
    {
        $content = <<<EOF
<IfModule mod_authz_core.c>
  Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
  Deny from all
</IfModule>
EOF;
        if ( (!is_dir($path)) || (!is_readable($path)) )
        {
        }
        file_put_contents("$path/.htaccess", $content);
    }
}
