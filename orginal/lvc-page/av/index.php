<?php
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
use wcf\system\WCF;
$user = WCF::getUser()->userID!==0;
if(!$user){echo('<script>alert("Sie müssen angemeldet sein!"); window.location="index.php"');}
$main = new Main();
$form = new Form();

$id = $_GET['id'] ?? 0;

if($id === 0){

}
if($id !== 0){

}