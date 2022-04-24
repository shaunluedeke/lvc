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
    {
        $this->sql->query("CREATE TABLE IF NOT EXISTS `history` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Date` VARCHAR(200) NOT NULL , `History` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `songs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , `Comments` TEXT NOT NULL , `Upvotes` INT(16) NOT NULL , `Downvotes` INT(16) NOT NULL , `Active` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `newsongs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `charts` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `SongIDs` TEXT NOT NULL , `Votes` TEXT NOT NULL , `ENDDate` VARCHAR(200) NOT NULL , `StartDate` VARCHAR(200) NOT NULL ,`Active` BOOLEAN NOT NULL, PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `songlogs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Date` VARCHAR(200) NOT NULL ,`New` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `contest` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Name` VARCHAR(200) NOT NULL , `Users` TEXT NOT NULL , `StartingDate` VARCHAR(200) NOT NULL , `EndingDate` VARCHAR(200) NOT NULL , `Activ` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `api` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `IP` VARCHAR(255) NOT NULL , `Permission` INT(16) NOT NULL , `Active` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `broadcastdate` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Weekday` INT(10) NOT NULL , `Delay` INT(10) NOT NULL , `Time` VARCHAR(200), `Link` VARCHAR(200) NOT NULL , `Name` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
    }

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
        $result = $this->sql->result(($this->date === "" ? "SELECT `Date`,`History` FROM `history`":"SELECT `History` FROM `history` WHERE `Date`='$this->date'"));
        $a = array();
        foreach (($result) as $row) {
            if (!isset($row["Date"])) {
                try {
                    return json_decode($row["History"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
            }
            try {
                $a[$row["Date"]] = json_decode($row["History"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
        }
        return $a;
    }

    public function add($data): void
    {
        $this->date = ($this->date !== "" ? $this->date : date("d.m.Y"));
        $a = !is_array($data) ? $data : $this->get();
        if (!is_array($data)) {
            $a[] = $data;
        }
        if (empty($this->get())) {
            try {
                $this->sql->query("INSERT INTO `history`(`ID`, `Date`, `History`) VALUES (null,'$this->date','" . json_encode($a, JSON_THROW_ON_ERROR) . "')");
            } catch (\JsonException $e) {
            }
        } else {
            try {
                $this->sql->query("UPDATE `history` SET `History`='" . json_encode($a, JSON_THROW_ON_ERROR) . "' WHERE `Date`='" . $this->date . "'");
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
    public function get(): array
    {
        $sql = $this->id === 0 ? "SELECT * FROM `songs`" : "SELECT * FROM `songs` WHERE `ID`='$this->id'";
        $result = $this->sql->result($sql);
        $a = array();
        if ($this->id === 0) {
            foreach ($result as $row) {
                $a[$row["ID"]]["id"] = $row["ID"];
                $a[$row["ID"]]["name"] = Main::addSymbol($row["Songname"]);
                try {
                    $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["file"] = $row["Songfile"];
                try {
                    $a[$row["ID"]]["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
                    $a[$row["ID"]]["upvotes"] = json_decode($row["Upvotes"], true, 512, JSON_THROW_ON_ERROR);
                    $a[$row["ID"]]["downvotes"] = json_decode($row["Downvotes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }

                $a[$row["ID"]]["active"] = (bool)$row["Active"];
            }
        } else {
            foreach ($result as $row) {
                $a["id"] = $row["ID"];
                $a["name"] = Main::addSymbol($row["Songname"]);
                try {
                    $a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["file"] = $row["Songfile"];
                try {
                    $a["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
                    $a["upvotes"] = json_decode($row["Upvotes"], true, 512, JSON_THROW_ON_ERROR);
                    $a["downvotes"] = json_decode($row["Downvotes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["active"] = (bool)$row["Active"];
            }
        }
        return $a;
    }

    public function getAll(int $offset = -1, int $limit = -1, string $name = ""): array
    {
        $namestring = $name === "" ? "" : " AND locate('$name',name)>0 ";
        $limitstring = $limit === -1 ? "" : " LIMIT " . $limit;
        $offsetstring = $offset === -1 ? "" : " OFFSET " . $offset;
        $result = $this->sql->result("SELECT * FROM `songs` ORDER BY `Upvotes` DESC" . $limitstring . $offsetstring . $namestring);
        $a = array();
        foreach ($result as $row) {
            $a[$row["ID"]]["id"] = $row["ID"];
            $a[$row["ID"]]["name"] = Main::addSymbol($row["Songname"]);
            try {
                $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["file"] = $row["Songfile"];
            try {
                $a[$row["ID"]]["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["ID"]]["upvotes"] = json_decode($row["Upvotes"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["ID"]]["downvotes"] = json_decode($row["Downvotes"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["active"] = (bool)$row["Active"];
        }
        return $a;
    }

    public function add(string $name, array $info, string $file): int
    {
        $id = -1;
        try {
            $id = $this->generateSongID();
            $file = "http://lvcharts.de/songdata/" . $id . "-" . $file;
            $this->sql->query("INSERT INTO `songs`(`ID`, `Songname`, `Songinfo`, `Songfile`, `Comments`, `Upvotes`, `Downvotes`, `Active`) VALUES" .
                " ('$id','$name','" . json_encode($info, JSON_THROW_ON_ERROR) . "','$file','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','1')");
        } catch (\JsonException $e) {
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
        $this->sql->query("DELETE FROM `songs` WHERE `ID`='$this->id'");
        $this->main->getLog()->danger("Der Song $this->id wurde erfolgreich entfernt", "Song Removed");
        return empty($this->get());
    }

    public function updateActive(bool $active)
    {
        if (empty($this->get())) {
            return false;
        }
        if ($this->get()["active"] !== $active) {
            $this->sql->query("UPDATE `songs` SET `Active`='$active' WHERE `ID`='$this->id'");
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
        $this->sql->query("UPDATE `songs` SET `Songname`='$name',`Songinfo`='$infostring',`Active`='$as' WHERE `ID`='$this->id'");
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
            $this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
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
            $this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
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
        $array = $downvotes ? $this->get()["downvotes"] : $this->get()["upvotes"];
        $array[$userid] = $amount;
        try {
            $sql = !$downvotes ? "UPDATE `songs` SET `Upvotes`='" . json_encode($array, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'"
                : "UPDATE `songs` SET `Downvotes`='" . json_encode($array, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'";
            $this->sql->query($sql);
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
            $sql = !$downvotes ? "UPDATE `songs` SET `Upvotes`='" . json_encode($array, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'"
                : "UPDATE `songs` SET `Downvotes`='" . json_encode($array, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'";
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
            $a[$row["ID"]]["id"] = $row["ID"];
            $a[$row["ID"]]["name"] = $row["Songname"];
            try {
                $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["file"] = $row["Songfile"];
            try {
                $a[$row["ID"]]["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["ID"]]["upvotes"] = json_decode($row["Upvotes"], true, 512, JSON_THROW_ON_ERROR);
                $a[$row["ID"]]["downvotes"] = json_decode($row["Downvotes"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["votes"] = count($a[$row["ID"]]["upvotes"] ?? array());
            $a[$row["ID"]]["active"] = (bool)$row["Active"];
        }
        return $this->main->arrsort($a, "votes");
    }


    #endregion

    #region GenerateID

    private function generateSongID(): int
    {
        try {
            $i = random_int(100, 99999999);
            while (($this->sql->count("SELECT * FROM `songs` WHERE `ID`='$i'")) > 0) {
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
            while (($this->sql->count("SELECT * FROM `charts` WHERE `ID`='$i'")) > 0) {
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
        $result = $this->sql->result(($this->id === 0 ? "SELECT * FROM `newsongs`" : "SELECT * FROM `newsongs` WHERE `ID`='$this->id'"));
        $a = array();
        if ($this->id === 0) {
            foreach (($result) as $row) {
                $a[$row["ID"]]["id"] = $row["ID"];
                $a[$row["ID"]]["name"] = Main::addSymbol($row["Songname"]);
                try {
                    $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["file"] = $row["Songfile"];
            }
        } else {
            foreach (($result) as $row) {
                $a["id"] = $row["ID"];
                $a["name"] = Main::addSymbol($row["Songname"]);
                try {
                    $a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["file"] = $row["Songfile"];
            }
        }
        return $a;
    }

    public function add(string $name, array $info, string $file): void
    {
        try {
            $infos = json_encode($info, JSON_THROW_ON_ERROR);
             $this->sql->query("INSERT INTO `newsongs`(`ID`, `Songname`, `Songinfo`, `Songfile`) VALUES (null,'$name','$infos','$file')");
        } catch (\JsonException $e) {
        }
    }

    public function remove(): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $this->sql->query("DELETE FROM `newsongs` WHERE `ID`='$this->id'");
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
        $id = random_int(0, 99999999);
        try {
            $this->sql->query("INSERT INTO `charts`(`ID`, `SongIDs`, `Votes`, `ENDDate`, `StartDate`, `Active`)" .
                " VALUES ('$id','" . json_encode($songids, JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','$enddate','$startdate','0')");
            $this->main->getLog()->sucesse("Die Charts mit der ID $id wurde hinzugefügt", "Charts Added");
            return $id;
        } catch (\JsonException $e) {
            return -1;
        }
    }

    public function get(bool $onlyactive=false): array
    {
        $activ = $onlyactive ? " WHERE `Active`='1'" : "";
        $activ1 = $onlyactive ? " AND `Active`='1'" : "";
        $sql = $this->id === 0 ? "SELECT * FROM `charts`".$activ : "SELECT * FROM `charts` WHERE `ID`='$this->id'".$activ1;
        $result = $this->sql->result($sql);
        $a = array();
            foreach ($result as $row) {
                $a[$row["ID"]]["id"] = $row["ID"];
                try {
                    $a[$row["ID"]]["songid"] = json_decode($row["SongIDs"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                try {
                    $a[$row["ID"]]["votes"] = json_decode($row["Votes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["enddate"] = $row["ENDDate"];
                $a[$row["ID"]]["startdate"] = $row["StartDate"];
                $a[$row["ID"]]["active"] = (bool)$row["Active"];
                $a[$row["ID"]]["autoset"] = (int)$row["AutoSet"];
            }
        return $a;
    }

    public function deleteCharts(): bool
    {
        if (!empty($this->get())) {
            return $this->sql->query("DELETE FROM `charts` WHERE `ID`='$this->id'");
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
                $this->sql->query("UPDATE `charts` SET `Votes`='" . json_encode($votes, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
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
                $this->sql->query("UPDATE `charts` SET `Votes`='" . json_encode($votes, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
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
            $this->sql->query("UPDATE `charts` SET `Active`='" . (!(bool)$this->get()["active"]? 1:0) . "' WHERE `ID`='$this->id'");
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
        }
        return $ar;
    }

    public function isNewSong(int $songid): bool
    {
        $result = $this->sql->result("SELECT `SongIDs` FROM `charts`");
        $contains = 0;
        foreach($result as $row){
            try {
                $songids = json_decode($row["SongIDs"], true, 512, JSON_THROW_ON_ERROR);
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
        $result = $this->sql->result(($this->id === 0 ? "SELECT * FROM `songlogs` WHERE `New`='$new'" : "SELECT * FROM `songlogs` WHERE `ID`='$this->id' AND `New`='$new'"));
        $a = array();
        if ($this->id === 0) {
            foreach ($result as $row) {
                $a[$row["ID"]]["id"] = $row["ID"];
                $a[$row["ID"]]["name"] = $row["Songname"];
                try {
                    $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["date"] = ($row["Date"]);
                $a[$row["ID"]]["status"] = (bool)($row["New"]);
            }
        } else {
            foreach ($result as $row) {
                $a["id"] = $row["ID"];
                $a["name"] = $row["Songname"];
                try {
                    $a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["date"] = ($row["Date"]);
                $a["status"] = (bool)($row["New"]);
            }
        }
        return $a;
    }

    public function getAll(int $offset, int $limit, int $new = 2): array
    {
        $limitstring = $limit === -1 ? "" : " LIMIT " . $limit;
        $offsetstring = $offset === -1 ? "" : " OFFSET " . $offset;
        $result = $this->sql->result(($new !== 2 ? "SELECT * FROM `songlogs` WHERE `New`='$new' ORDER BY `ID` ASC" : "SELECT * FROM `songlogs` ORDER BY `ID` ASC") . $limitstring . $offsetstring);
        $a = array();
        foreach ($result as $row) {
            $a[$row["ID"]]["id"] = $row["ID"];
            $a[$row["ID"]]["name"] = $row["Songname"];
            try {
                $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["date"] = ($row["Date"]);
            $a[$row["ID"]]["status"] = (bool)($row["New"]);
        }
        return $a;
    }

    public function add(bool $new = false): bool
    {
        $infostring = "";
        $date = date("d.m.Y");
        $i = $this->main->getSong($this->id)->get();
        $name = $i["name"];
        try {
            $infostring = json_encode($i["info"], JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
        }
        $newstring = ($new ? 1 : 0);
        $this->sql->query("INSERT INTO `songlogs`(`ID`, `Songname`, `Songinfo`, `Date`, `New`) VALUES ('$this->id','$name','$infostring','$date','$newstring')");
        return true;
    }

    public function remove(): bool
    {
        if (empty($this->main->getSong($this->id)->get())) {
            return false;
        }
        $this->sql->query("DELETE FROM `songlogs` WHERE `ID`='$this->id'");
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

        $sql = $this->id === 0 ? "SELECT * FROM `contest` WHERE `Activ`='$active'" : "SELECT * FROM `contest` WHERE `ID`='$this->id'";
        $result = $this->sql->result($sql);
        try {
            if ($this->id === 0) {
                foreach ($result as $row) {
                    $ar[$row["ID"]]["id"] = (int)$row["ID"];
                    $ar[$row["ID"]]["name"] = $row["Name"];
                    $ar[$row["ID"]]["user"] = json_decode($row["Users"], true, 512, JSON_THROW_ON_ERROR);
                    $ar[$row["ID"]]["startdate"] = strtotime($row["StartingDate"]);
                    $ar[$row["ID"]]["enddate"] = strtotime($row["EndingDate"]);
                    $ar[$row["ID"]]["active"] = (bool)$row["Active"];
                }
            } else {
                foreach ($result as $row) {
                    $ar["id"] = (int)$row["ID"];
                    $ar["name"] = $row["Name"];
                    $ar["user"] = json_decode($row["Users"], true, 512, JSON_THROW_ON_ERROR);
                    $ar["startdate"] = strtotime($row["StartingDate"]);
                    $ar["enddate"] = strtotime($row["EndingDate"]);
                    $ar["active"] = (bool)$row["Active"];
                }
            }
        } catch (\JsonException $e) {
        }
        return $ar;
    }

    public function add(string $name, $startdate, $enddate): int
    {
        try {
            $i = random_int(1, 9999999);
            $this->sql->query("INSERT INTO `contest`(`ID`, `Name`, `Users`, `StartingDate`, `EndingDate`, `Activ`)" .
                " VALUES ('$i','$name','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','$startdate','$enddate','true')");
            return $i;
        } catch (\Exception $e) {
        }
        return 0;
    }

    public function remove(): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $this->sql->query("DELETE FROM `contest` WHERE `ID`='$this->id'");
        return empty($this->get());
    }

    public function updateStatus(bool $status = false): bool
    {
        return $this->sql->query("UPDATE `contest` SET `Activ`='$status' WHERE `ID`='$this->id'");
    }

    public function addUser($user): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $users = $this->get()["user"];
        $users[] = $user;
        try {
            return $this->sql->query("UPDATE `contest` SET `Users`='" . json_encode($users, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
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

        $result = $this->sql->result("SELECT `ID` FROM `broadcastdate` WHERE `NEXT`='1'");
        foreach ($result as $row){
            return $row['ID'];
        }
        return 1;
    }

    public function get(int $id = 0):array{
        $data = [];
        $result = $this->sql->result("SELECT * FROM `broadcastdate`".($id===0?"":" WHERE `ID`='".$id."'"));
        foreach ($result as $row){
            $data[$row['ID']] = array(
                "Weekday" => (int)$row['Weekday'],
                "Delay" => (int)$row['Delay'],
                "Time" => $row['Time'],
                "Link" => $row['Link'],
                "Name" => $row['Name']
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

    public function addDate($weekday, $delay, $time, $link, $name):void
    {
        $this->sql->query("INSERT INTO `broadcastdate`(`Weekday`, `Delay`, `Time`, `Link`, `Name`) VALUES ('$weekday','$delay','$time','$link','$name')");
    }

    public function removeDate(int $id):void{
        $this->sql->query("DELETE FROM `broadcastdate` WHERE `ID` = '$id'");
    }

}