<?php

namespace wcf\system\lvc;
use wcf\system\lvc\Main;

class Forwarding{

    private Main $main;
    public function __construct(){
        $this->main=new Main;
    }

    public function addSong($data): string
    {
        if (($data["files"]["songdata"]["type"] === "audio/wav") ||
            ($data["files"]["songdata"]["type"] === "audio/mp3") ||
            ($data["files"]["songdata"]["type"] === "audio/wma") ||
            ($data["files"]["songdata"]["type"] === "audio/aac") ||
            ($data["files"]["songdata"]["type"] === "audio/ogg")||
            ($data["files"]["songdata"]["type"] === "audio/mpeg")) {
            echo("1");
            if (!is_dir("/var/www/html/songdate") && !mkdir("/var/www/html/songdate/new", 0777, TRUE) && !is_dir("/var/www/html/songdate/new")) {
                echo sprintf('Directory "%s" was not created', "/var/www/html/songdate");
            }
            echo("2");
            $newfilename = $data["songauthor"]."-".$data["songname"].".". pathinfo($data["files"]["songdata"]['name'])['extension'];
            echo($newfilename);
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdate/new/" . $newfilename)) {
                echo("3");
                $info = array();
                $info["uploaddate"] = date("d.M.Y");
                $info["author"] = $data["songauthor"];
                $info["infotxt"] = $data["songinfo"];

                if ($this->main->addNewSong($data["songname"], $info, "http://lvcharts.de/songdata/new/" . $newfilename)) {
                    echo("4");
                    return "./index.php?song-add/&status=success";
                }
                return "./index.php?song-add/&status=error&error=1003";
            }
            return "./index.php?song-add/&status=error&error=1002";
        }
        return "./index.php?song-add/&status=error&error=1001";
    }

    public function addAdminSong($data):string
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
            $id = $this->main->addSong($data["songname"], $info, $data["files"]["songdata"]["basename"]);
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdate/" . $id . "-" . $data["files"]["songdata"]["basename"])) {
                return "./index.php?admin/&page=addsong&status=success&id=" . $id;
            }
            return "./index.php?admin/&page=addsong&status=error&error=1002";


        }
        return "./index.php?admin/&page=addsong&status=error&error=1001";

    }

    public function addAV($data):string
    {
        $i = $this->main->createCharts($data["startdate"],$data["enddate"],$data["songid"]);
        if($i!==-1){
            return "index.php?admin&page=av&id=$i";
        }
        return"index.php?admin&page=av&status=error";
    }

    public function addComment($data,$username):string{
        $this->main->addSongComment($data["songid"], $username , $data["newcomment"]);
        return "index.php?song/&id" . $data["songid"];
    }
}