<?php

$cats = [];
$cats[] = [ "name" => 'Frisky', "favorite_show" => 'Cats and Dogs' ];
$cats[] = [ "name" => 'Meowie', "favorite_show" => 'Dogs chasing Cats' ];
$cats[] = [ "name" => 'Caesar', "favorite_show" => 'All Cats Rule' ];
$cats[] = [ "name" => 'Dot', "favorite_show" => 'No cats suck' ];

usort($cats, function($a, $b) { return strcmp($a->favorite_show, $b->favorite_show); });

var_dump($cats);

?>
