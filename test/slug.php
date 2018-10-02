<?php

include('config.php');
include(lbkinclude.'utils.php');

header("Content-type: text/plain");

$url = 'Ésta es una cadena de prueba';
$url2 = strtolower($url);
echo $url2;
echo ' => ';
echo clean_url($url);

$url2 = ' El € está muy    caro';
echo " => ";
echo clean_url($url2);

$url3 = '¿por qué    lo hiciste?';
echo ' => ';
echo clean_url($url3);


$url4 = 'Los pingüinos son como 10 niños';
echo ' => ';
echo clean_url($url4);

?>

