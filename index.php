<?php

session_start();

/*use wcf\system\WCF;
$wcf_user = WCF::getUser();
$wcf_user_id = $wcf_user->userID;
$userGroup = $wcf_user->getGroupIDs();
global $api;

$user = WCF::getUser(1);
echo $user->username;

$user = $api->loadClass("User"); */
require_once(__DIR__ ."/lvc-api/php/user.1.0.class.php");
require(__DIR__ ."/lvc-api/api/template/template.php");
require_once(__DIR__ ."/lvc-api/php/form.class.php");


$template = new template();
$main = new User_1_0();
$form = new form();

$template->setTempFolder(__DIR__."/lvc-page/");

$site = $_GET['site']??"";

switch($site){
    case "login":{
        if(!(isset($_SESSION["login"])&&$_SESSION["login"]===1)){
            if(isset($_POST["loginname"], $_POST["loginpw"])){
                $_SESSION["login"]=1;
                $_SESSION["name"]=$_POST["loginname"];
            }
        }else{
            unset($_SESSION["name"],$_SESSION["login"]);
        }
        break;
    }
}

$template->assign("login",(isset($_SESSION["login"])&&$_SESSION["login"]===1));
$template->assign("login1",(isset($_SESSION["login"])&&$_SESSION["login"]===1));


$form->setUrl("index.php?site=login");
$form->addInput("Name or Email", "Please enter your Name or Email","loginname","",true);
$form->addPassword("Password", "Please enter your Password","loginpw","",true);
$form->addSubmit("Send","","loginsend","loginsend");
$template->assign("logintxt",$form->show());

if(!$main->init()){echo("DB ERROR!");}

$template->parse("/main/index.tpl");
