<?php

namespace wcf\system\lvc;

class Main
{
    public SQL $sql;

    public function __construct()
    {
        $this->sql = new SQL();
    }

    public function getChart(int $id = 0):charts
    {
        return new charts($id);
    }

    public function getContest(int $id = 0):contest
    {
        return new contest($id);
    }

    public function getLogs(int $id = 0):songlogs
    {
        return new songlogs($id);
    }

    public function getNewSong(int $id = 0):newsong
    {
        return new newsong($id);
    }

    public function getSong(int $id = 0):song
    {
        return new song($id);
    }

    public function getHistory(string $date = ""):history
    {
        return new history($date);
    }

    public function getAd(string $file=""):ad{
        return new ad($file);
    }

    public function getScan(array $ortner,$type):scanDir{
        return new scanDir($ortner,$type);
    }

    public function getReadText(string $file=""):readText{
        return new readText($file);
    }

    public function getLog():Log{
        return new Log();
    }

    public function getAPI($ip=""):API{
        return new API($ip);
    }

    public function getBrodcastdate():brodcastdates{
        return new brodcastdates();
    }

    public function init(): void
    {}

    public static function removeSymbol(string $txt):string{
        return str_replace(array("ä", "ü", "ö", "Ä", "Ü", "Ö", "ß", "´","§","&", "'"),
            array("&auml;", "&uuml;", "&ouml;", "&Auml;", "&Uuml;", "&Ouml;", "&szlig;", "","&sect;","&amp;","&apos;"), $txt);
    }

    public static function addSymbol(string $txt):string
    {
        return str_replace(array("&auml;", "&uuml;", "&ouml;", "&Auml;", "&Uuml;", "&Ouml;", "&szlig;", "´","&sect;","&amp;","&apos;"),
            array("ä", "ü", "ö", "Ä", "Ü", "Ö", "ß", "","§","&", "'"), $txt);
    }

    public static function deleteSymbol(string $txt):string
    {
        return str_replace(array("ä", "ü", "ö", "Ä", "Ü", "Ö", "ß", "´","§","&", "'"),
            array("", "", "", "", "", "", "", "","","",""), $txt);
    }

    public function aasort (&$array, $key):array {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter, SORT_NUMERIC);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
        return $array;
    }

    public function arrsort (&$array, $key):array {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        arsort($sorter, SORT_NUMERIC);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
        return $array;
    }
}

class Log{

    private Discord $webhook;
    public function __construct()
    {
        $this->webhook = new Discord();
    }

    public function alert(string $message,string $title=""):void{
        $this->webhook->setTitle($title===""?"ALERT":$title);
        $this->webhook->setTxt($message);
        $this->webhook->setColor("fcba03");
        $this->webhook->send();
    }

    public function danger(string $message,string $title=""):void{
        $this->webhook->setTitle($title===""?"DANGER":$title);
        $this->webhook->setTxt($message);
        $this->webhook->setColor("FF0000");
        $this->webhook->send();
    }

    public function info(string $message,string $title=""):void{
        $this->webhook->setTitle($title===""?"INFO":$title);
        $this->webhook->setTxt($message);
        $this->webhook->setColor("03fcfc");
        $this->webhook->send();
    }

    public function sucesse(string $message,string $title=""):void{
        $this->webhook->setTitle($title===""?"SUCESSE":$title);
        $this->webhook->setTxt($message);
        $this->webhook->setColor("17fc03");
        $this->webhook->send();
    }
}

class history
{
    private SQL $sql;
    private string $date;

    public function __construct(string $date = "")
    {
        $this->date = $date;
        $this->sql = new SQL();
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function get(): array
    {
        $result = $this->sql->result(($this->date === "" ? "SELECT `created_at`,`content` FROM `histories`":"SELECT `content` FROM `histories` WHERE `created_at`='$this->date'"));
        $a = array();
        foreach (($result) as $row) {
            if (!isset($row["created_at"])) {
                try {
                    return json_decode($row["content"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
            }
            try {
                $a[$row["created_at"]] = json_decode($row["content"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
        }
        return $a;
    }

    public function add($data): void
    {
        $a = !is_array($data) ? $data : $this->get();
        if (!is_array($data)) {
            $a[] = $data;
        }
        if (empty($this->get())) {
            try {
                $this->sql->query("INSERT INTO `histories`(`content`) VALUES ('" . json_encode($a, JSON_THROW_ON_ERROR) . "')");
            } catch (\JsonException $e) {
            }
        } else {
            try {
                $this->sql->query("UPDATE `histories` SET `content`='" . json_encode($a, JSON_THROW_ON_ERROR) . "' WHERE `created_at`='" . $this->date . "'");
            } catch (\JsonException $e) {
            }
        }
    }

}

class song
{
    private Main $main;
    private int $id;
    private SQL $sql;

    public function __construct(int $id = 0)
    {
        $this->main = new Main();
        $this->id= $id;
        $this->sql = new SQL();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #region Info

    public function exists():bool{
        return $this->sql->count("SELECT `id` FROM `songs` WHERE `id`='$this->id'") > 0;
    }

    public function get(): array
    {
        $sql = $this->id === 0 ? "SELECT * FROM `songs`" : "SELECT * FROM `songs` WHERE `id`='$this->id'";
        $result = $this->sql->result($sql);
        $a = array();
        if ($this->id === 0) {
            foreach ($result as $row) {
                $a[$row["id"]]["id"] = $row["id"];
                $a[$row["id"]]["name"] = Main::addSymbol($row["name"]);
                try {
                    $a[$row["id"]]["info"] = json_decode($row["info"], true, 512, JSON_THROW_ON_ERROR);
                    if(!isset($a[$row["id"]]["info"]["uploaddate"])){
                        $a[$row["id"]]["info"]["uploaddate"] = $row["created_at"];
                    }
                } catch (\JsonException $e) {
                }
                $a[$row["id"]]["file"] = $row["file"];
                try {
                    $a[$row["id"]]["comments"] = json_decode($row["comments"], true, 512, JSON_THROW_ON_ERROR);
                    $a[$row["id"]]["upvotes"] = json_decode($row["likes"], true, 512, JSON_THROW_ON_ERROR);
                    $a[$row["id"]]["downvotes"] = json_decode($row["dislikes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }

                $a[$row["id"]]["active"] = (bool)$row["is_active"];
            }
        } else {
            foreach ($result as $row) {
                $a["id"] = $row["id"];
                $a["name"] = Main::addSymbol($row["name"]);
                try {
                    $a["info"] = json_decode($row["info"], true, 512, JSON_THROW_ON_ERROR);
                    if(!isset($a["info"]["uploaddate"])){
                        $a["info"]["uploaddate"] = $row["created_at"];
                    }
                } catch (\JsonException $e) {
                }
                $a["file"] = $row["file"];
                try {
                    $a["comments"] = json_decode($row["comments"], true, 512, JSON_THROW_ON_ERROR);
                    $a["upvotes"] = json_decode($row["likes"], true, 512, JSON_THROW_ON_ERROR);
                    $a["downvotes"] = json_decode($row["dislikes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["active"] = (bool)$row["is_active"];
            }
        }
        return $a;
    }

    public function getAll(int $offset = -1, int $limit = -1, string $name = ""): array
    {
        $namestring = $name === "" ? "" : " AND locate('$name',name)>0 ";
        $limitstring = $limit === -1 ? "" : " LIMIT " . $limit;
        $offsetstring = $offset === -1 ? "" : " OFFSET " . $offset;
        $result = $this->sql->result("SELECT * FROM `songs` ORDER BY `likes` DESC" . $limitstring . $offsetstring . $namestring);
        $a = array();
        foreach ($result as $row) {
            $a[$row["id"]]["id"] = $row["id"];
            $a[$row["id"]]["name"] = Main::addSymbol($row["name"]);
            try {
                $a[$row["id"]]["info"] = json_decode($row["info"], true, 512, JSON_THROW_ON_ERROR);
                if(!isset($a[$row["id"]]["info"]["uploaddate"])){
                    $a[$row["id"]]["info"]["uploaddate"] = $row["created_at"];
                }
            } catch (\JsonException $e) {
            }
            $a[$row["id"]]["file"] = $row["file"];
            try {
                $a[$row["id"]]["comments"] = json_decode($row["comments"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["id"]]["upvotes"] = json_decode($row["likes"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["id"]]["downvotes"] = json_decode($row["dislikes"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["id"]]["active"] = (bool)$row["is_active"];
        }
        return $a;
    }

    public function add(string $name, array $info, string $file): int
    {
        try {
            $id = $this->sql->queryID("INSERT INTO `songs`( `name`, `info`, `file`, `comments`, `likes`, `dislikes`, `is_active`) VALUES" .
                " ('$name','" . json_encode($info, JSON_THROW_ON_ERROR) . "','$file','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','1')");
            $file = "http://lvcharts.de/songdata/" . $id . "-" . $file;
            $this->sql->query("UPDATE `songs` SET 'file'='$file' WHERE `id`='$id'");
        } catch (\JsonException $e) {
            return -1;
        }
        $this->id = $id;
        $this->main->getLog()->sucesse("Der Song $name wurde erfolgreich hinzugefügt ID: $id", "Song Added");
        return $id;
    }

    public function remove(): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $this->main->getLogs($this->id)->add();
        $this->sql->query("DELETE FROM `songs` WHERE `id`='$this->id'");
        $this->main->getLog()->danger("Der Song $this->id wurde erfolgreich entfernt", "Song Removed");
        return empty($this->get());
    }

    public function updateActive(bool $active)
    {
        if (empty($this->get())) {
            return false;
        }
        if ($this->get()["active"] !== $active) {
            $this->sql->query("UPDATE `songs` SET `is_active`='$active' WHERE `id`='$this->id'");
        }
    }

    public function edit(string $name, array $info, bool $active):void{
        if (empty($this->get())) {
            return;
        }
        $as = $active ? 1:0;
        $infostring = "";
        try {
            $infostring = json_encode($info, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
        }
        $this->sql->query("UPDATE `songs` SET `name`='$name',`info`='$infostring',`is_active`='$as' WHERE `id`='$this->id'");
        $this->main->getLog()->info("Der Song $this->id wurde erfolgreich geupdatet", "Song Updatet");
    }

    #endregion

    #region Song Comment
    public function addComment($name, $comment):bool
    {
        if (empty($this->get())) {
            return false;
        }
        $allcomments = $this->get()["comments"];
        $a = array();
        $a["name"] = $name;
        $a["comment"] = $comment;
        $a["time"] = date("H:i d.m.Y");
        $allcomments[$this->generateCommentID()] = $a;
        try {
            $this->sql->query("UPDATE `songs` SET `comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'");
            return true;
        } catch (\JsonException $e) {
        }
        return false;
    }

    public function removeComment($commentid):bool
    {
        if (empty($this->get())) {
            return false;
        }
        $allcomments = $this->get()["comments"];
        if (!array_key_exists($commentid, $allcomments)) {
            return false;
        }
        unset($allcomments[$commentid]);
        try {
            $this->sql->query("UPDATE `songs` SET `comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'");
            return true;
        } catch (\JsonException $e) {
        }
        return false;
    }
    #endregion

    #region SongVote

    public function getVoting($downvotes = false): int
    {
        return !$downvotes ? count($this->get()["upvotes"]??array()) : count($this->get()["downvotes"]??array());
    }

    public function addVote(int $userid,int $amount = 1, bool $downvotes = false): bool
    {
        if($this->hasVoted($userid,$downvotes)){
            return $this->removeVote($userid,$amount,$downvotes);
        }
        $likes = $this->get()["upvotes"];
        $dislikes = $this->get()["downvotes"];
        if ($downvotes) {
            $dislikes[$userid] = $amount;
            if(array_key_exists($userid, $likes)){
                unset($likes[$userid]);
            }
        }else{
            $likes[$userid] = $amount;
            if(array_key_exists($userid, $dislikes)){
                unset($dislikes[$userid]);
            }
        }
        try {
            $this->sql->query("UPDATE `songs` SET `likes`='" . json_encode($likes, JSON_THROW_ON_ERROR) . "',`dislikes`='" . json_encode($dislikes, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'");
            return true;
        } catch (\JsonException $e) {
        }
        return false;
    }

    public function removeVote(int $userid,int $amount = 1, bool $downvotes = false): bool
    {
        if(!$this->hasVoted($userid,$downvotes)){
            return false;
        }
        $array = $downvotes ? $this->get()["downvotes"] : $this->get()["upvotes"];
        unset($array[$userid]);
        try {
            $sql = !$downvotes ? "UPDATE `songs` SET `likes`='" . json_encode($array, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'"
                : "UPDATE `songs` SET `dislikes`='" . json_encode($array, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'";
            $this->sql->query($sql);
            return true;
        } catch (\JsonException $e) {
        }
        return false;
    }

    public function hasVoted(int $userid, bool $downvotes = false): bool
    {
        $votes = ($downvotes ? $this->get()["downvotes"] : $this->get()["upvotes"]) ?? array();
        return array_key_exists($userid, $votes);
    }

    public function getTopSongs(int $limit = -1, int $offset = -1): array
    {
        $result = $this->sql->result("SELECT * FROM `songs`" . ($limit === -1 ? "" : " LIMIT " . $limit) . ($offset === -1 ? "" : " OFFSET " . $offset));
        $a = array();
        foreach ($result as $row) {
            $a[$row["id"]]["id"] = $row["id"];
            $a[$row["id"]]["name"] = $row["name"];
            try {
                $a[$row["id"]]["info"] = json_decode($row["info"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["id"]]["file"] = $row["file"];
            try {
                $a[$row["id"]]["comments"] = json_decode($row["comments"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["id"]]["upvotes"] = json_decode($row["likes"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["id"]]["downvotes"] = json_decode($row["dislikes"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["id"]]["votes"] = count($a[$row["id"]]["likes"] ?? array());
            $a[$row["id"]]["active"] = (bool)$row["is_active"];
        }
        return $this->main->arrsort($a, "votes");
    }


    #endregion

    #region GenerateID

    private function generateSongID(): int
    {
        try {
            $i = random_int(100, 99999999);
            while (($this->sql->count("SELECT * FROM `songs` WHERE `id`='$i'")) > 0) {
                $i = random_int(100, 99999999);
            }
            return $i;
        } catch (\Exception $e) {
        }
        return $this->generateSongID();
    }

    private function generateCommentID(): int
    {

        try {
            $i = random_int(100, 99999999);
            while (($this->sql->count("SELECT * FROM `charts` WHERE `id`='$i'")) > 0) {
                $i = random_int(100, 99999999);
            }
            return $i;
        } catch (\Exception $e) {
        }
        return $this->generateCommentID();
    }

    #endregion

}

class newsong
{

    private int $id;
    private Main $main;
    public SQL $sql;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->main = new Main();
        $this->sql = $this->main->sql;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function get(): array
    {
        $result = $this->sql->result(($this->id === 0 ? "SELECT * FROM `new_songs`" : "SELECT * FROM `new_songs` WHERE `id`='$this->id'"));
        $a = array();
        if ($this->id === 0) {
            foreach (($result) as $row) {
                $a[$row["id"]]["id"] = $row["id"];
                $a[$row["id"]]["name"] = Main::addSymbol($row["name"]);
                try {
                    $a[$row["id"]]["info"] = json_decode($row["info"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["id"]]["file"] = $row["file"];
            }
        } else {
            foreach (($result) as $row) {
                $a["id"] = $row["id"];
                $a["name"] = Main::addSymbol($row["name"]);
                try {
                    $a["info"] = json_decode($row["info"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["file"] = $row["file"];
            }
        }
        return $a;
    }

    public function add(string $name, array $info, string $file): void
    {
        try {
            $infos = json_encode($info, JSON_THROW_ON_ERROR);
             $this->sql->query("INSERT INTO `new_songs`(`id`, `name`, `info`, `file`) VALUES (null,'$name','$infos','$file')");
        } catch (\JsonException $e) {
        }
    }

    public function remove(): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $this->sql->query("DELETE FROM `new_songs` WHERE `id`='$this->id'");
        return empty($this->get());
    }

    public function accept(): bool
    {
        $info = $this->get();
        if (empty($info)) {
            return false;
        }
        $file = pathinfo(file($info["file"]))['filename'];
        $newid = $this->main->getSong()->add($info["name"], $info["info"], $file);
        rename("/var/www/html/songdate/new/" . $file, "/var/www/html/songdate/" . $newid . "-" . $file);
        $this->main->getLogs($this->id)->add(true);
        $this->remove();
        return false;
    }
}

class charts
{
    private int $id;
    private Main $main;
    public SQL $sql;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->main = new Main();
        $this->sql = $this->main->sql;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function create(string $startdate, string $enddate, array $songids): int
    {
        try {
            $id = $this->sql->queryID("INSERT INTO `charts`(`song_ids`, `votes`, `end_date`, `start_date`, `is_active`)" .
                " VALUES ('" . json_encode($songids, JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','$enddate','$startdate','0')");
            $this->main->getLog()->sucesse("Die Charts mit der ID $id wurde hinzugefügt", "Charts Added");
            return $id;
        } catch (\JsonException $e) {
            return -1;
        }
    }

    public function get(bool $onlyactive=false): array
    {
        $activ = $onlyactive ? " WHERE `is_active`='1'" : "";
        $activ1 = $onlyactive ? " AND `is_active`='1'" : "";
        $sql = $this->id === 0 ? "SELECT * FROM `charts`".$activ : "SELECT * FROM `charts` WHERE `id`='$this->id'".$activ1;
        $result = $this->sql->result($sql);
        $a = array();
            foreach ($result as $row) {
                $a[$row["id"]]["id"] = $row["id"];
                try {
                    $a[$row["id"]]["songid"] = json_decode($row["song_ids"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                try {
                    $a[$row["id"]]["votes"] = json_decode($row["votes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["id"]]["enddate"] = $row["end_date"];
                $a[$row["id"]]["startdate"] = $row["start_date"];
                $a[$row["id"]]["showdate"] = $row["show_date"];
                $a[$row["id"]]["canbeshown"] = strtotime($row["show_date"])<=time();
                $a[$row["id"]]["active"] = (bool)$row["is_active"];
                $a[$row["id"]]["autoset"] = (int)$row["autoset"];
            }
        return $a;
    }

    public function deleteCharts(): bool
    {
        if (!empty($this->get())) {
            return $this->sql->query("DELETE FROM `charts` WHERE `id`='$this->id'");
        }
        return false;
    }

    public function addVote(int $songid, int $userid, int $amount): bool
    {
        $infos = $this->get()[$this->id] ?? array();
        $votes = $infos["votes"] ?? array();

        if (array_key_exists($userid, $votes)) {
            if (count($votes[$userid]) > 3) {
                return false;
            }
            if (array_key_exists($songid, $votes[$userid])) {
                return false;
            }
        }
        $votes[$userid][$songid] = $amount;
        if ($infos["active"]) {
            try {
                $this->sql->query("UPDATE `charts` SET `votes`='" . json_encode($votes, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'");
                return true;
            } catch (\JsonException $e) {
            }
        }
        return false;
    }

    public function addAdminVote(int $songid, int $amount, int $id=0): int
    {
        $infos = $this->get()[$this->id] ?? array();
        $votes = $infos["votes"] ?? array();
        if($id === 0) {
            try {
                $id = random_int(10000000, 9999999999999);
            } catch (\Exception $e) {}
        }
        $votes[$id][$songid] = $amount;
        if ($infos["active"]) {
            try {
                $this->sql->query("UPDATE `charts` SET `votes`='" . json_encode($votes, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'");
            } catch (\JsonException $e) {
            }
        }
        return $id;
    }

    public function hasVoted(int $userid): bool
    {
        $infos = $this->get()[$this->id];
        $votes = $infos["votes"] ?? array();
        if (array_key_exists($userid, $votes)) {
            return count($votes[$userid]) >= 3;
        }
        return false;
    }

    public function getVotesfromUser(int $userid): array
    {
        $infos = $this->get()[$this->id];
        return ($infos["votes"][$userid] ?? array());
    }

    public function changeActive(): bool
    {
        if (!empty($this->get())) {
            $this->sql->query("UPDATE `charts` SET `is_active`='" . (!$this->get()["active"]? 1:0) . "' WHERE `id`='$this->id'");
            return true;
        }
        return false;
    }

    public function isStarted(): bool
    {
        if (!empty($this->get())) {
            return (int)$this->get()[$this->id]["autoset"]===1;
        }
        return false;
    }

    public function isEnded(): bool
    {
        if (!empty($this->get())) {
            return (int)$this->get()[$this->id]["autoset"]===2;
        }
        return false;
    }

    public function getTopSongs(): array
    {
        $ar = array();
        if($this->id === 0) {
            $cl = $this->get();
            foreach ($cl as $c => $cv) {
                foreach ($cv["votes"] as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $num = ($ar[$cv["id"]][$key2]) ?? 0;
                        $num += $value2;
                        $ar[$cv["id"]][$key2] = $num;
                    }
                }
                foreach($cv["songid"] as $key){
                    $num = ($ar[$cv["id"]][$key]) ?? 0;
                    if($num !== 0) {
                        continue;
                    }
                    $ar[$cv["id"]][$key] = $num;
                }
            }
            foreach ($ar[$cv["id"]] as $key => $v){
                if(!in_array($key, $cv["songid"], true)) {
                    unset($ar[$cv["id"]][$key]);
                    continue;
                }

                if(!$this->main->getSong($key)->exists()){
                    unset($ar[$cv["id"]][$key]);
                }
            }
        }else{
            $cl = $this->get();
            foreach ($cl[$this->id]["votes"] as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    $num = ($ar[$key2]) ?? 0;
                    $num += $value2;
                    $ar[$key2] = $num;
                }
            }
            foreach($cl[$this->id]["songid"] as $key){
                $num = ($ar[$key]) ?? 0;
                if($num !== 0) {
                    continue;
                }
                $ar[$key] = $num;
            }
            foreach ($ar as $key => $v){
                if(!in_array($key, $cl[$this->id]["songid"], true)) {
                    unset($ar[$key]);
                }
                if(!$this->main->getSong($key)->exists()){
                    unset($ar[$key]);
                }
            }
        }
        return $ar;
    }

    public function isNewSong(int $songid): bool
    {
        $result = $this->sql->result("SELECT `song_ids` FROM `charts`");
        $contains = 0;
        foreach($result as $row){
            try {
                $songids = json_decode($row["song_ids"], true, 512, JSON_THROW_ON_ERROR);
                if(in_array($songid, $songids, true)){
                    $contains++;
                    if($contains >= 2){
                        return false;
                    }
                }
            } catch (\JsonException $e) {
            }
        }
        return true;
    }


}

class songlogs
{
    private int $id;
    private Main $main;
    public SQL $sql;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->main = new Main();
        $this->sql = $this->main->sql;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function get(bool $new = false): array
    {
        $new1 = ($new ? "1" : "0");
        $result = $this->sql->result(($this->id === 0 ? "SELECT * FROM `song_logs` WHERE `status_id`='$new1'" : "SELECT * FROM `song_logs` WHERE `id`='$this->id' AND `status_id`='$new1'"));
        $a = array();
        if ($this->id === 0) {
            foreach ($result as $row) {
                $a[$row['id']] = [
                    "id" => $row["id"],
                    "songid" => $row["song_id"],
                    "song" => $this->main->getSong($row["song_id"])->get(),
                    "status_id" => $row["status_id"],
                    "date" => $row["created_at"],
                ];
            }
        } else {
            foreach ($result as $row) {
                $a = [
                    "id" => $row["id"],
                    "songid" => $row["song_id"],
                    "song" => $this->main->getSong($row["song_id"])->get(),
                    "status_id" => $row["status_id"],
                    "date" => $row["created_at"],
                ];
            }
        }
        return $a;
    }


    public function add(bool $new = false): bool
    {
        $newstring = ($new ? 1 : 0);
        $this->sql->query("INSERT INTO `song_logs`(`song_id`, `status_id`) VALUES ('$this->id', '$newstring')");
        return true;
    }

    public function remove(): bool
    {
        if (empty($this->main->getSong($this->id)->get())) {
            return false;
        }
        $this->sql->query("DELETE FROM `song_logs` WHERE `id`='$this->id'");
        return empty($this->main->getSong($this->id)->get());
    }

}

class contest
{
    private int $id;
    private Main $main;
    public SQL $sql;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->main = new Main();
        $this->sql = $this->main->sql;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function get(bool $active = false): array
    {
        $ar = array();

        $sql = $this->id === 0 ? "SELECT * FROM `contests` WHERE `is_active`='$active'" : "SELECT * FROM `contests` WHERE `id`='$this->id'";
        $result = $this->sql->result($sql);
        try {
            if ($this->id === 0) {
                foreach ($result as $row) {
                    $ar[$row["id"]]["id"] = (int)$row["id"];
                    $ar[$row["id"]]["name"] = $row["name"];
                    $ar[$row["id"]]["user"] = json_decode($row["user_ids"], true, 512, JSON_THROW_ON_ERROR);
                    $ar[$row["id"]]["startdate"] = strtotime($row["start_date"]);
                    $ar[$row["id"]]["enddate"] = strtotime($row["end_date"]);
                    $ar[$row["id"]]["active"] = (bool)$row["is_active"];
                }
            } else {
                foreach ($result as $row) {
                    $ar["id"] = (int)$row["id"];
                    $ar["name"] = $row["name"];
                    $ar["user"] = json_decode($row["user_ids"], true, 512, JSON_THROW_ON_ERROR);
                    $ar["startdate"] = strtotime($row["start_date"]);
                    $ar["enddate"] = strtotime($row["end_date"]);
                    $ar["active"] = (bool)$row["is_active"];
                }
            }
        } catch (\JsonException $e) {
        }
        return $ar;
    }

    public function add(string $name, $startdate, $enddate): int
    {
        try {
           return $this->sql->queryID("INSERT INTO `contests`( `name`, `user_ids`, `start_date`, `end_date`, `is_active`)" .
                " VALUES ('$name','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','$startdate','$enddate','1')");
        } catch (\Exception $e) {
        }
        return 0;
    }

    public function remove(): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $this->sql->query("DELETE FROM `contests` WHERE `id`='$this->id'");
        return empty($this->get());
    }

    public function updateStatus(bool $status = false): bool
    {
        return $this->sql->query("UPDATE `contests` SET `is_active`='$status' WHERE `id`='$this->id'");
    }

    public function addUser($user): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $users = $this->get()["user"];
        $users[] = $user;
        try {
            return $this->sql->query("UPDATE `contests` SET `user_ids`='" . json_encode($users, JSON_THROW_ON_ERROR) . "' WHERE `id`='$this->id'");
        } catch (\JsonException $e) {
        }
        return false;
    }
}

class ad{

    private string $file = "";
    private Main $main;

    public function __construct($file="")
    {
        $this->main = new Main();
        $this->file = $file===""?$this->getFile():$file;
    }

    public function getTitle(): string
    {
        if($this->file === ""){
            return "Default";
        }
        return $this->main->getReadText($this->file)->getArray()[0] ?? "";
    }

    public function getText(): string
    {
        $imglink = "";
        $txt = $this->main->getReadText($this->file)->getArray();
        $offset = 1;
        if($this->is_url($txt[1]??"")){
            $imglink = "<img src='$txt[1]' alt='$txt[0] Logo' class='logobox1'><br>";
            $offset=2;
        }
        return $imglink.($this->main->getReadText($this->file)->getString($offset));
    }

    public function getFile():string
    {
        $file=$this->file;
        $scan = $this->main->getScan(array("/var/www/html/ad"),array("txt"));
        if($file==="" || !file_exists("/var/www/html/ad/".$this->file)){
            $filescan = $scan->scan();
            if($filescan === false){
                return "";
            }
            $id=0;try {$id = random_int(0, count($filescan) - 1);} catch (\Exception $e) {}
            $file = $filescan[$id] ?? "";
        }
        return $file;
    }
    private function is_url($uri):bool{
        return(preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$uri));
    }
}

class scanDir
{

    private array $directories, $files;
    private $ext_filter;


    public function __construct(array $directories, $ext_filter = false)
    {
        $this->directories = $directories;
        $this->ext_filter = $ext_filter;
    }

    public function scan()
    {
        if(!$this->verifyPaths($this->directories)){
            return false;
        }
        return $this->files;
    }

    private function verifyPaths($paths): bool
    {
        $path_errors = array();
        if (is_string($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $this->directories[] = $path;
                $this->find_contents($path);
            } else {
                $path_errors[] = $path;
            }
        }
        if ($path_errors) {
            return false;
        }
        return true;
    }

    private function find_contents($dir): void
    {
        $result = array();
        $root = scandir($dir);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file($dir . DIRECTORY_SEPARATOR . $value)) {
                if (!$this->ext_filter || in_array(strtolower(pathinfo($dir . DIRECTORY_SEPARATOR . $value, PATHINFO_EXTENSION)), $this->ext_filter, true)) {
                    $this->files[] = $result[] = $dir . DIRECTORY_SEPARATOR . $value;
                }
            }
        }
    }

    public function find_by_name($dir, $name, $txt = false)
    {
        $root = scandir($dir);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file($dir . DIRECTORY_SEPARATOR . $value) && strtolower(pathinfo($dir . DIRECTORY_SEPARATOR . $value)['filename']) === strtolower($name)) {
                return pathinfo($dir . DIRECTORY_SEPARATOR . $value)['extension'];
            }
        }
        return "";
    }
}

class readText
{

    private string $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getString($lineofset=0):string
    {
        $txt = "";
        $txtarray = $this->getArray();
        for ($i = $lineofset, $iMax = count($txtarray); $i< $iMax; $i++){
            $txt.= $txtarray[$i]."<br>";
        }
        return $txt;

    }

    public function getArray(): array
    {
        $txt = array();
        if (file_exists($this->file) && $fileman = fopen($this->file, 'rb')) {
            while (!feof($fileman)) {
                $txt[]=fgets($fileman);
            }
        }
        return $txt;
    }


}

class API{

    private Main $main;
    private string $ip;
    private SQL $sql;

    public function __construct(string $ip="")
    {
        $this->main = new Main();
        $this->ip=$ip;
        $this->sql = new SQL();
    }

    public function getIP():string
    {
        return $this->ip;
    }

    public function setIP(string $ip): void
    {
        $this->ip = $ip;
    }

    public function hasPermission(int $permission):bool{
        $result = $this->sql->result("SELECT * FROM `api` WHERE `IP`='".$this->ip."'");
        foreach ($result as $row){
            if($row['Active']===1 && $row['Permission']>=$permission){
                return true;
            }
        }
        return false;
    }

    public function noPermission(int $permission):void{
        $this->main->getLog()->danger("API: ".$this->ip." tried to access with permission ".$permission, "API");
    }

    public function add($ip,$permission):void{
        $this->sql->query("INSERT INTO `api` (`IP`, `Permission`,`Active`) VALUES ('".$ip."', '".$permission."','1')");
    }

    public function remove():void{
        $this->sql->query("DELETE FROM `api` WHERE `IP`='".$this->ip."'");
    }

    public function update($permission):void{
        $this->sql->query("UPDATE `api` SET `Permission`='".$permission."' WHERE `IP`='".$this->ip."'");
    }

    public function updateActive(int $active):void{
        $this->sql->query("UPDATE `api` SET `Active`='".$active."' WHERE `IP`='".$this->ip."'");
    }

    public function get():array{
        $result = $this->sql->result("SELECT * FROM `api`".($this->ip===""?"":" WHERE `IP`='".$this->ip."'"));
        $data = array();
        if($this->ip==="") {
            foreach ($result as $row) {
                $data[$row["ID"]] = array(
                    "IP" => $row['IP'],
                    "Permission" => (int)$row['Permission'],
                    "Active" => (bool)$row['Active']
                );
            }
        }else{
            foreach ($result as $row) {
                $data = array(
                    "IP" => $row['IP'],
                    "Permission" => (int)$row['Permission'],
                    "Active" => (bool)$row['Active']
                );
            }
        }
        return $data;
    }
}

class brodcastdates{

    private SQL $sql;

    public function __construct()
    {
        $this->sql = new SQL();
    }

    public function getNextDate():int{

        $result = $this->sql->result("SELECT `id` FROM `brodcastdates` WHERE `NEXT`='1'");
        foreach ($result as $row){
            return $row['id'];
        }
        return 1;
    }

    public function get(int $id = 0):array{
        $data = [];
        $result = $this->sql->result("SELECT * FROM `brodcastdates`".($id===0?"":" WHERE `id`='".$id."'"));
        foreach ($result as $row){
            $data[$row['id']] = array(
                "Weekday" => (int)$row['weekday'],
                "Delay" => (int)$row['delay'],
                "Time" => $row['time'],
                "Link" => $row['link'],
                "Name" => $row['name']
            );
        }
        return $data;
    }

    public function getDayofInt($int = "1"):string
    {
        $day = [
            "1" => "Montag",
            "2" => "Dienstag",
            "3" => "Mittwoch",
            "4" => "Donnerstag",
            "5" => "Freitag",
            "6" => "Samstag",
            "7" => "Sonntag"
        ];
        return $day[$int];
    }

    public function addDate($weekday, $delay, $time, $link, $name,$lastbrodcast):void
    {
        $this->sql->query("INSERT INTO `brodcastdates`(`name`, `weekday`, `delay`, `time`, `link`, `last_brodcast`) VALUES ('$name','$weekday','$delay','$time','$link','$lastbrodcast')");
    }

    public function removeDate(int $id):void{
        $this->sql->query("DELETE FROM `brodcastdates` WHERE `id` = '$id'");
    }

}