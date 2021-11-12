<?php

session_start();

use wcf\system\WCF;
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
$wcf_user = WCF::getUser();
$wcf_user_id = $wcf_user->userID;
$userGroup = $wcf_user->getGroupIDs();

$user = WCF::getUser();
echo $user->username;

$form = new Form();

$form->addText("das ist ein Test");

echo($form->show());

$main = new Main();

try{
    $main->init();
    echo("<pre>");
    echo("</pre>");

}catch (Exception $e){
    echo($e->getMessage()."<br><br>");
    echo($e->getTraceAsString());
    die();
}
