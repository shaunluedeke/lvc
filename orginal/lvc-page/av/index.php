<?php
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
use wcf\system\WCF;
$user = WCF::getUser()->userID!==0;
if(!$user){header("Location: index.php" );}
$main = new Main();
$form = new Form();

$id = $_GET['id'] ?? 0;
$chart = $main->getChart($id);

if($id === 0){

}

if($id !== 0){

}