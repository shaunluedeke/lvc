<?php

namespace wcf\system\lvc;

use wcf\system\lvc\Main;

class Forwarding
{

    private Main $main;

    public function __construct()
    {
        $this->main = new Main;
    }

    public function addSong($data): string
    {
        if ($this->check_file_is_audio(pathinfo($data["files"]["songdata"]['name'])['extension'])) {
            echo("1");
            if (!is_dir("/var/www/html/songdate") && !mkdir("/var/www/html/songdata/new", 0777, TRUE) && !is_dir("/var/www/html/songdate/new")) {
                echo sprintf('Directory "%s" was not created', "/var/www/html/songdata");
            }
            echo("2");
            $newfilename = Main::deleteSymbol($data["songauthor"] . "-" . $data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension'];
            echo($newfilename);
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/new/" . $newfilename)) {
                echo("3");
                $info = array();
                $info["uploaddate"] = date("d.m.Y");
                $info["author"] = Main::removeSymbol($data["songauthor"]);
                $info["infotxt"] = Main::removeSymbol($data["songinfo"]);

                if ($this->main->getNewSong()->add($data["songname"], $info, "http://lvcharts.de/songdata/new/" . $newfilename)) {
                    echo("4");
                    return "./index.php?song-add/&status=success";
                }
                return "./index.php?song-add/&status=error&error=1003";
            }
            return "./index.php?song-add/&status=error&error=1002";
        }
        return "./index.php?song-add/&status=error&error=1001";
    }

    public function addAdminSong($data): string
    {
        if ($this->check_file_is_audio(pathinfo($data["files"]["songdata"]['name'])['extension'])) {
            $info = array();
            $info["uploaddate"] = date("d.M.Y");
            $info["author"] = Main::removeSymbol($data["songauthor"]);
            $info["infotxt"] = Main::removeSymbol($data["songinfo"]);
            $id = $this->main->getSong()->add(Main::removeSymbol($data["songname"]), $info, Main::deleteSymbol($data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension']);
            if ($id < 0) {
                return "./index.php?admin/&page=addsong&status=error&error=1003";
            }
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/" . $id . "-" .
                Main::deleteSymbol($data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension'])) {
                echo("3<br>");
                return "./index.php?admin/&page=addsong&status=success&id=" . $id;
            }
            return "./index.php?admin/&page=addsong&status=error&error=1002";
        }
        return "./index.php?admin/&page=addsong&status=error&error=1001";

    }

    public function addAV($data): string
    {
        $a = array();
        if (count(explode(",", $data["songids"])) > 1) {
            foreach (explode(",", $data["songids"]) as $id) {
                $a[] = (int)$id;
            }
        } else {
            $a[] = (int)$data["songids"];
        }
        $i = $this->main->getChart()->create($data["startdate"], $data["enddate"], $a);
        if ($i !== -1) {
            return "index.php?admin&page=av&id=$i";
        }
        return "index.php?admin&page=av&status=error";
    }

    public function addComment($data, $username): string
    {
        $this->main->getSong((int)$data["songid"])->addComment(Main::removeSymbol($username), Main::removeSymbol($data["newcomment"]));
        return "index.php?song/&id=" . $data["songid"];
    }

    public function search($data): string
    {
        $url = "";

        $limit = $data["limit"] ?? 10;

        if ((int)$limit !== 10) {
            $url = "&limit=" . $limit;
        }

        $name = $data["name"];
        if ($name !== "") {
            $url .= "&name=" . ($name);
        }

        return "./index.php?song/" . $url;
    }

    public function setAV(int $id, int $userid, $data): string
    {
        if ($id !== 0) {
            $charts = $this->main->getChart($id);
            if (count($charts->get()) > 0) {
                foreach ($data as $key => $value) {
                    if (strpos($key, "voting/") === 0) {
                        $songid = (int)explode("/", $key)[1];
                        if (($songid !== 0) && ((int)$value !== 0) && $charts->addVote($songid, $userid, (int)$value)) {
                            return "index.php?av&id=$id&status=success";
                        }
                    }
                }
            }
        }
        return "index.php?av&id=$id&status=error";
    }

    public function editAdminSong($data):string{
        $song = $this->main->getSong($data["id"]);
        $info = $song->get();
        if(count($info)>0){
            $info = ($info["info"]);
            $info["author"] = Main::removeSymbol($data["author"]);
            $info["infotxt"] = Main::removeSymbol($data["infotxt"]);
            $song->edit(Main::removeSymbol($data["name"]), $info, ($data["status"] ?? "Active") === "Active");
        }
        return "index.php?admin&page=songedit&id=" . $data["id"];
    }

    private function check_file_is_audio($tmp): bool
    {
        $allowed = array('aac', 'ac3', 'act', 'aif', 'aiff', 'mp3', 'mpa', 'wav', 'wma', 'ogg','flac', 'rm', 'mpeg');
        if (in_array($tmp, $allowed, true)) {
            return true;
        }
        return false;

    }
}