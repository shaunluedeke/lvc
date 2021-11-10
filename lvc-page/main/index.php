<?php

session_start();

use wcf\system\WCF;
$wcf_user = WCF::getUser();
$wcf_user_id = $wcf_user->userID;
$userGroup = $wcf_user->getGroupIDs();
global $api;

$user = WCF::getUser(1);
echo $user->username;

$user = $api->loadClass("User");
if(!$user->init()){echo("DB ERROR!");}

echo("test");
?>