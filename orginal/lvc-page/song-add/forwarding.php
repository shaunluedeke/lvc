<?php

use wcf\system\lvc\Main;

class Forwarding {
    private $http_refere;
    private $main;
    function __construct($wcf_user_id, $type, $data) {
        $main = new Main();
        if($type === "songadd"){
            if(isset($data["files"]["songdata"])) {
                $this->addSong($wcf_user_id, $data);
            }else{
                $this->http_refere = "./index.php?head-add/&status=error&error=1003";
            }
        }

    }

    function addSong($wcf_user_id,$data){
        if(($data["files"]["songdata"]["type"]==="audio/wav")||
             ($data["files"]["songdata"]["type"]==="audio/mp3")||
             ($data["files"]["songdata"]["type"]==="audio/wma")||
             ($data["files"]["songdata"]["type"]==="audio/aac")||
             ($data["files"]["songdata"]["type"]==="audio/ogg")){
                 if(!is_dir("/var/www/html/songdate") && !mkdir("/var/www/html/songdate/new", 0777, TRUE) && !is_dir("/var/www/html/songdate/new")) {
                     throw new \RuntimeException(sprintf('Directory "%s" was not created', "/var/www/html/songdate"));
                 }
                 if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdate/new/" .$data["files"]["songdata"]["basename"])) {

                     $info = array();
                     $info["uploaddate"] = date("d.M.Y");
                     $info["author"] = $data["songauthor"];
                     $info["infotxt"] = $data["songinfo"];

                     if ($this->main->addNewSong($data["songname"], $info,"http://lvcharts.de/songdata/new/".$data["files"]["songdata"]["basename"])) {
                         $this->http_refere = "./index.php?song-add/&status=success";
                     } else {
                         $this->http_refere = "./index.php?song-add/&status=error&error=1003";
                     }
                 }else{
                     $this->http_refere = "./index.php?song-add/&status=error&error=1002";
                 }
         }else{$this->http_refere = "./index.php?head-add/&status=error&error=1001";}
    }

    function getReferer($http_refere) {
        return $this->http_refere;
    }

    /*function isDebug() {
        return true;
    }*/
}
?>