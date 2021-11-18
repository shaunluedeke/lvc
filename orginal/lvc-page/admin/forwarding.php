<?php

use wcf\system\lvc\Main;

class Forwarding
{
    private string $http_refere;
    private Main $main;

    function __construct($wcf_user_id, $type, $data)
    {
        $this->main = new Main();
        if ($type === "songadd") {
            if (isset($data["files"]["songdata"])) {
                $this->addSong($wcf_user_id, $data);
            } else {
                $this->http_refere = "./index.php?head-add/&status=error&error=1003";
            }
        }
        if($type === "avadd"){
            $this->addAV($wcf_user_id, $data);
        }

    }

    function addSong($wcf_user_id, $data)
    {
        if (($data["files"]["songdata"]["type"] === "audio/wav") ||
            ($data["files"]["songdata"]["type"] === "audio/mp3") ||
            ($data["files"]["songdata"]["type"] === "audio/wma") ||
            ($data["files"]["songdata"]["type"] === "audio/aac") ||
            ($data["files"]["songdata"]["type"] === "audio/ogg")) {
            if (!is_dir("/var/www/html/songdate") && !mkdir("/var/www/html/songdate", 0777, TRUE) && !is_dir("/var/www/html/songdate")) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', "/var/www/html/songdate"));
            }
            $info = array();
            $info["uploaddate"] = date("d.M.Y");
            $info["author"] = $data["songauthor"];
            $info["infotxt"] = $data["songinfo"];
            $id = $this->main->addSong($data["songname"], $info,  $data["files"]["songdata"]["basename"]);
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdate/" . $id . "-" . $data["files"]["songdata"]["basename"])) {
                $this->http_refere = "./index.php?admin/&page=addsong&status=success&id=".$id;
            } else {
                $this->http_refere = "./index.php?admin/&page=addsong&status=error&error=1002";
            }

        } else {
            $this->http_refere = "./index.php?admin/&page=addsong&status=error&error=1001";
        }
    }

    function addAV($wcf_user_id, $data)
    {
       $i = $this->main->createCharts($data["startdate"],$data["enddate"],$data["songid"]);
       if($i!==-1){
           $this->http_refere="index.php?admin&page=av&id=$i";
       }else{
           $this->http_refere="index.php?admin&page=av&status=error";
       }
    }

    function getReferer($http_refere)
    {
        return $this->http_refere;
    }

    /*function isDebug() {
        return true;
    }*/
}

?>