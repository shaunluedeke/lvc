<?php

session_start();

use wcf\system\WCF;
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$main->init();


$form = new Form();

$form->addTitle("Moin");
$form->addText("das ist ein Test<br>ja es geht");

echo($form->show());