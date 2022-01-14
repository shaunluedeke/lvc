<?php


session_start();

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$main->init();
$form = new Form();

$ad = $main->getAd();

$form->addTitle($ad->getTitle());
$form->addText($ad->getText());

echo($form->show());