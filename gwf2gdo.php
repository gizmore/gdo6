<?php
use GDO\File\Filewalker;

chdir(__DIR__);
require 'GDO6.php';

Filewalker::traverse('protected/', '/config.*\\.php/', function($entry, $path){

    $count = 0;

    $contents = file_get_contents($path);
    $contents = str_replace('GWF_', 'GDO_', $contents, $count);

    if ($count)
    {
        file_put_contents($path, $contents);
        echo "Converted config file: $entry.\n";
    }
    else
    {
        echo "Config was converted already: $entry.\n";
    }
});

echo "All done.\n";
