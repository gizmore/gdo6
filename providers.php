<?php
/**
 * This prints all modules and their providers.
 * The list can be copied by gdo6 authors to Core/ModuleProviders.php
 */

use GDO\File\Filewalker;
use GDO\Util\Common;

# Use gdo6 core
include "GDO6.php";

Filewalker::traverse("GDO", null, false, function($entry, $fullpath) {
   if (is_dir('GDO/'.$entry."/.git"))
   {
       $c = file_get_contents('GDO/'.$entry."/.git/config");
       $c = Common::regex('#/gizmore/([-_a-z0-9]+)#m', $c);
       echo "'".$entry."' => '$c',\n";
   }
},  0);
