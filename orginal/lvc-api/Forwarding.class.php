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
        if (($data["files"]["songdata"]["type"] === "audio/wav") ||
            ($data["files"]["songdata"]["type"] === "audio/mp3") ||
            ($data["files"]["songdata"]["type"] === "audio/wma") ||
            ($data["files"]["songdata"]["type"] === "audio/aac") ||
            ($data["files"]["songdata"]["type"] === "audio/ogg") ||
            ($data["files"]["songdata"]["type"] === "audio/mpeg")) {
            echo("1");
            if (!is_dir("/var/www/html/songdate") && !mkdir("/var/www/html/songdata/new", 0777, TRUE) && !is_dir("/var/www/html/songdate/new")) {
                echo sprintf('Directory "%s" was not created', "/var/www/html/songdata");
            }
            echo("2");
            $newfilename = $data["songauthor"] . "-" . $data["songname"] . "." . pathinfo($data["files"]["songdata"]['name'])['extension'];
            echo($newfilename);
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/new/" . $newfilename)) {
                echo("3");
                $info = array();
                $info["uploaddate"] = date("d.M.Y");
                $info["author"] = $data["songauthor"];
                $info["infotxt"] = $data["songinfo"];

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
        echo($data["files"]["songdata"]["type"]);
        if (($data["files"]["songdata"]["type"] === "audio/wav") ||
            ($data["files"]["songdata"]["type"] === "audio/mp3") ||
            ($data["files"]["songdata"]["type"] === "audio/wma") ||
            ($data["files"]["songdata"]["type"] === "audio/aac") ||
            ($data["files"]["songdata"]["type"] === "audio/ogg") ||
            ($data["files"]["songdata"]["type"] === "audio/mpeg")) {
            $info = array();
            $info["uploaddate"] = date("d.M.Y");
            $info["author"] = $data["songauthor"];
            $info["infotxt"] = $data["songinfo"];
            $id = $this->main->getSong()->add($data["songname"], $info, $data["songname"] . "." . pathinfo($data["files"]["songdata"]['name'])['extension']);
            if ($id < 0) {
                return "./index.php?admin/&page=addsong&status=error&error=1003";
            }
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/" . $id . "-" .
                $data["songname"] . "." . pathinfo($data["files"]["songdata"]['name'])['extension'])) {
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
        $this->main->getSong((int)$data["songid"])->addComment($username, $data["newcomment"]);
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
}