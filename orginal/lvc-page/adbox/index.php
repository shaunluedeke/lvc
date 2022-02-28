<?php
if(!isset($_SESSION)){
    session_start();
}
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$ad = $main->getAd();

$form = new Form();
$form->addTitle($ad->getTitle());
$form->addText('<style>.logobox1{max-height: 120px;min-height: 40px;}</style>');
$form->addText($ad->getText());

echo($form->show());
?>