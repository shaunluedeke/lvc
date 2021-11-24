<?php
use wcf\system\lvc\Form;

$form = new Form();

$form->addTitle("Search");
$prosite = array(10,25,50,100);
$name = $_GET['name'] ?? "";
$limit = $_GET['limit'] ?? 10;
$limit = $limit < 1 ? 1 : $limit;
$limit = $limit<=100 ? (!($limit<10) ? $limit : 10) : 100;

$form->addInput("Suche...", "", "boxsearch",$name);
$form->addSelect("Anzahl", "", "boxamount", ...$prosite);
$form->setSelect($limit);
$form->addButton("Senden", "button", "songsearch");
echo($form->show());