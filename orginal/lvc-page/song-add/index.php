<?php
session_start();

//use wcf\system\WCF;
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
//$wcf_user = WCF::getUser();

$form = new Form();

if(!true){
    $form->addTitle("Login ERROR");
    $form->addText("Please Login first");

    echo($form->show());
}else{
    //$userGroup = $wcf_user->getGroupIDs();

    $main = new Main();

    $form->addTitle("New Song upload");
    $form->addInput("Song Name","","songname","",true);
    $form->addInput("Song Author","","songauthor","",true);
    $form->addUpload("Song Datei","New Data","songdata",".mp3 .wav .aac .wma",true);
    $form->addTextarea("Song Infos","","songinfo","",true);
    $form->addButton("Hinzufügen", "button", "songadd");

    echo($form->show());
}
