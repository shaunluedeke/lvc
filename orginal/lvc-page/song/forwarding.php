<?php

use wcf\system\lvc\Main;

class Forwarding {
    private string $http_refere;
    private Main $main;
    function __construct($wcf_user_id, $type, $data) {
        $this->main = new Main();
        if($type === "addcomment"){
            $this->addComment($wcf_user_id, $data);
        }

    }

    function addComment($wcf_user_id,$data){
        $name = wcf\system\WCF::getUser()->userName;
        $this->main->addSongComment($data["songid"], $name, $data["newcomment"]);
        $this->http_refere="index.php?song/&id".$data["songid"];
    }

    function getReferer($http_refere) {
        return $this->http_refere;
    }

    /*function isDebug() {
        return true;
    }*/
}
?>