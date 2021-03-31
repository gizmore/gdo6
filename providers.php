<?php
/**
 * This prints all modules and their providers.
 * The list can be copied by gdo6 authors to Core/ModuleProviders.php
 */

use GDO\File\Filewalker;
use GDO\Util\Common;

# Use gdo6 core
include "GDO6.php";

global $mode;
$mode = $argv[1];

Filewalker::traverse("GDO", null, false, function($entry, $fullpath) {
   if (is_dir('GDO/'.$entry."/.git"))
   {
       global $mode;
       $c = file_get_contents('GDO/'.$entry."/.git/config");
       $c = Common::regex('#/gizmore/([-_a-z0-9]+)#m', $c);
       if ($mode)
       {
           echo "$entry - <https://github.com/gizmore/$c>\n\n";
       }
       else
       {
           echo "'".$entry."' => '$c',\n";
       }
   }
},  0);
