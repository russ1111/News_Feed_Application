<?php
//xpath-test.php


$file = 'xml/catalog.xml';

$xml = simplexml_load_file($file);

/*
echo '<pre>';
var_dump($xml);
echo '</pre>';
*/


$zep = $xml->xPath('/catalog/cd[artist="Led Zeppelin"] ');

/*
echo '<pre>';
var_dump($zep);
echo '</pre>';
*/

foreach($zep as $cd)
{
    echo "<p>Artist:$cd->artist Album Title:$cd->title Year:$cd->year</p>";
}


















