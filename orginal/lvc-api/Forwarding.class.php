<?php

namespace wcf\system\lvc;

class Forwarding
{

    private Main $main;

    public function __construct()
    {
        $this->main = new Main;
    }

    public function addSong($data,bool $admin): string
    {
        if(!$admin) {
            if ($this->check_file_is_audio(pathinfo($data["files"]["songdata"]['name'])['extension'])) {
                $newfilename = Main::deleteSymbol($data["songauthor"] . "-" . $data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension'];
                if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/new/" . $newfilename)) {
                    $info = array();
                    $info["uploaddate"] = date("d.m.Y");
                    $info["author"] = Main::removeSymbol($data["songauthor"]);
                    $info["infotxt"] = Main::removeSymbol($data["songinfo"]);

                    $this->main->getNewSong()->add($data["songname"], $info, "http://lvcharts.de/songdata/new/" . $newfilename);
                    return "./index.php?song-add/&status=success";
                }
                return "./index.php?song-add/&status=error&error=1002";
            }
            return "./index.php?song-add/&status=error&error=1001";
        }else {
            if ($this->check_file_is_audio(pathinfo($data["files"]["songdata"]['name'])['extension'])) {
                $info = array();
                $info["uploaddate"] = date("d.m.Y");
                $info["author"] = Main::removeSymbol($data["songauthor"]);
                $info["infotxt"] = Main::removeSymbol($data["songinfo"]);
                $id = $this->main->getSong()->add(Main::removeSymbol($data["songname"]), $info, Main::deleteSymbol($data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension']);
                if ($id < 0) {
                    return "./index.php?admin/&page=addsong&status=error&error=1003";
                }
                if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/" . $id . "-" .
                    Main::deleteSymbol($data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension'])) {
                    return "./index.php?admin/&page=addsong&status=success&id=" . $id;
                }
                return "./index.php?admin/&page=addsong&status=error&error=1002";
            }
            return "./index.php?admin/&page=addsong&status=error&error=1001";
        }
    }

    public function addAdminSong($data): string
    {
        if ($this->check_file_is_audio(pathinfo($data["files"]["songdata"]['name'])['extension'])) {
            $info = array();
            $info["uploaddate"] = date("d.m.Y");
            $info["author"] = Main::removeSymbol($data["songauthor"]);
            $info["infotxt"] = Main::removeSymbol($data["songinfo"]);
            $id = $this->main->getSong()->add(Main::removeSymbol($data["songname"]), $info, Main::deleteSymbol($data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension']);
            if ($id < 0) {
                return "./index.php?admin/&page=addsong&status=error&error=1003";
            }
            if (move_uploaded_file($data["files"]["songdata"]['tmp_name'], "/var/www/html/songdata/" . $id . "-" .
                Main::deleteSymbol($data["songname"]) . "." . pathinfo($data["files"]["songdata"]['name'])['extension'])) {
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
                $voteings = [];
                foreach ($data as $key => $value) {
                    if (strpos($key, "voting/") === 0) {
                        $songid = (int)(explode("/", $key)[1] ?? 0);
                        if (($songid !== 0) && ((int)($value ?? 0) !== 0)) {
                            $value = ((int)$value === 1 ? 3 : ((int)$value === 3 ? 1 : $value));
                            if(($voteings[$value] ?? 0) === 0){
                                $voteings[$value] = $songid;
                            }else{
                                return "index.php?av&id=$id&status=error&error=101";
                            }
                        }
                    }
                }
                if(count($voteings)!==3){
                    return "index.php?av&id=$id&status=error&error=102";
                }
                foreach ($voteings as $key => $value){
                    if(!$charts->addVote($value, $userid, (int)$key)){
                        return "index.php?av&id=$id&status=error&error=103";
                    }
                }
            }
        }
        return "index.php?av&id=$id&status=success";
    }

    public function setAVAdmin(int $id, $data):string{
        if ($id !== 0) {
            $charts = $this->main->getChart($id);
            if (count($charts->get()) > 0) {
                $charid = 0;
                foreach ($data as $key => $value) {
                    if (strpos($key, "voting/") === 0) {
                        $songid = (int)explode("/", $key)[1];
                        if (($songid !== 0) && ((int)$value !== 0)) {
                            $a = (int)$value === 3 ? 1 : $value;
                            $value = (int)$value === 1 ? 3 : $a;
                            $charid = $charts->addAdminVote($songid, (int)$value,$charid);
                        }
                    }
                }
            }
        }
        return "index.php?admin&page=av-vote&status=success";
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

    public function downloadnewAdminSong(array $data): string
    {
        $song = $this->main->getNewSong($data["id"]);
        $info = $song->get();
        if(count($info)>0){
            echo('<script>window.open('.$info["file"].', "_blank");</script>');
        }
        return "index.php?admin&page=newsong&id=" . $data["id"];
    }

    public function deletenewAdminSong(array $data): string
    {
        $song = $this->main->getNewSong($data["id"]);
        $info = $song->get();
        if(count($info)>0){
            unlink($info["file"]);
            $song->remove();
        }
        return "index.php?admin&page=newsong";
    }

    public function addnewAdminSong(array $data):string
    {
        $song = $this->main->getNewSong($data["id"]);
        $info = $song->get();
        if(count($info)>0) {
            $song->accept();
        }
        return "index.php?admin&page=newsong";
    }

    public function addAPI(array $data):string
    {
        $this->main->getAPI()->add($data["ip"], $data["permission"]);
        return "index.php?admin&page=api";
    }

    public function updateAPI(array $data):string
    {
        $api = $this->main->getAPI($data["ip"]);
        $api->update($data["permission"]);
        $api->updateActive($data["active"]);
        return "index.php?admin&page=api";
    }

    public function addBCD(array $data):string
    {
        $dcb = $this->main->getBrodcastdate();
        $weekday = 1;
        switch ($data["weekday"] ?? "Mo") {
            case "Di":
                $weekday = 2;
                break;
            case "Mi":
                $weekday = 3;
                break;
            case "Do":
                $weekday = 4;
                break;
            case "Fr":
                $weekday = 5;
                break;
            case "Sa":
                $weekday = 6;
                break;
            case "So":
                $weekday = 7;
                break;
        }

        $dcb->addDate($weekday, $data["delay"],$data["time"], $data["link"], $data["name"]);
        return "index.php?admin&page=bcd";
    }
}