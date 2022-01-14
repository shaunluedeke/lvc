<?php

namespace wcf\system\lvc;

use wcf\system\lvc\SQL;

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

    public function init(): void
    {
        $this->sql->query("CREATE TABLE IF NOT EXISTS `history` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Date` VARCHAR(200) NOT NULL , `History` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `songs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , `Comments` TEXT NOT NULL , `Upvotes` INT(16) NOT NULL , `Downvotes` INT(16) NOT NULL , `Active` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `newsongs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `charts` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `SongIDs` TEXT NOT NULL , `Votes` TEXT NOT NULL , `ENDDate` VARCHAR(200) NOT NULL , `StartDate` VARCHAR(200) NOT NULL ,`Active` BOOLEAN NOT NULL, PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `songlogs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Date` VARCHAR(200) NOT NULL ,`New` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
        $this->sql->query("CREATE TABLE IF NOT EXISTS `contest` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Name` VARCHAR(200) NOT NULL , `Users` TEXT NOT NULL , `StartingDate` VARCHAR(200) NOT NULL , `EndingDate` VARCHAR(200) NOT NULL , `Activ` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
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
                $a[$row["ID"]]["name"] = $row["Songname"];
                try {
                    $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["file"] = $row["Songfile"];
                try {
                    $a[$row["ID"]]["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["upvotes"] = (int)($row["Upvotes"]);
                $a[$row["ID"]]["downvotes"] = (int)$row["Downvotes"];
                $a[$row["ID"]]["active"] = (bool)$row["Active"];
            }
        } else {
            foreach ($result as $row) {
                $a["id"] = $row["ID"];
                $a["name"] = $row["Songname"];
                try {
                    $a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["file"] = $row["Songfile"];
                try {
                    $a["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["upvotes"] = (int)($row["Upvotes"]);
                $a["downvotes"] = (int)$row["Downvotes"];
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
            $a[$row["ID"]]["name"] = $row["Songname"];
            try {
                $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["file"] = $row["Songfile"];
            try {
                $a[$row["ID"]]["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["upvotes"] = (int)($row["Upvotes"]);
            $a[$row["ID"]]["downvotes"] = (int)$row["Downvotes"];
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
                " ('$id','$name','" . json_encode($info, JSON_THROW_ON_ERROR) . "','$file','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','0','0','1')");
            $this->main->getLogs($this->id)->add(true);
        } catch (\JsonException $e) {
        }
        $this->id = $id;
        return $id;
    }

    public function remove(): bool
    {
        if (empty($this->get())) {
            return false;
        }
        $this->main->getLogs($this->id)->add();
        $this->sql->query("DELETE FROM `songs` WHERE `ID`='$this->id'");
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
            return $this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
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
            return $this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
        } catch (\JsonException $e) {
        }
        return false;
    }
    #endregion

    #region SongVote

    public function getVoting($downvotes = false): int
    {
        return !$downvotes ? $this->get()["upvotes"] : $this->get()["downvotes"];
    }

    public function addVote(int $amount = 1, bool $downvotes = false): void
    {
        $sql = !$downvotes ? "UPDATE `songs` SET `Upvotes`='" . ($this->getVoting($downvotes) + $amount) . "' WHERE `ID`='$this->id'"
            : "UPDATE `songs` SET `Downvotes`='" . ($this->getVoting($downvotes) + $amount) . "' WHERE `ID`='$this->id'";
        $this->sql->query($sql);
    }

    public function removeVote(int $amount = 1, bool $downvotes = false): void
    {
        $sql = !$downvotes ? "UPDATE `songs` SET `Upvotes`='" . ($this->getVoting($downvotes) - $amount) . "' WHERE `ID`='$this->id'"
            : "UPDATE `songs` SET `Downvotes`='" . ($this->getVoting($downvotes) - $amount) . "' WHERE `ID`='$this->id'";
        $this->sql->query($sql);
    }

    public function getTopSongs(int $limit = -1, int $offset = -1, bool $downvotes = false): array
    {
        $limitstring = $limit === -1 ? "" : " LIMIT " . $limit;
        $offsetstring = $offset === -1 ? "" : " OFFSET " . $offset;
        $sql = !$downvotes ? "SELECT * FROM `songs` ORDER BY `Upvotes` DESC" . $limitstring . $offsetstring : "SELECT * FROM `songs` ORDER BY `Downvotes` DESC" . $limitstring . $offsetstring;
        $result = $this->sql->result($sql);
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
            } catch (\JsonException $e) {
            }
            $a[$row["ID"]]["upvotes"] = (int)($row["Upvotes"]);
            $a[$row["ID"]]["downvotes"] = (int)$row["Downvotes"];
            $a[$row["ID"]]["active"] = (bool)$row["Active"];
        }
        return $a;
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
                $a[$row["ID"]]["name"] = $row["Songname"];
                try {
                    $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["file"] = $row["Songfile"];
            }
        } else {
            foreach (($result) as $row) {
                $a["id"] = $row["ID"];
                $a["name"] = $row["Songname"];
                try {
                    $a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["file"] = $row["Songfile"];
            }
        }
        return $a;
    }

    public function add(string $name, array $info, string $file): bool
    {
        try {
            $infos = json_encode($info, JSON_THROW_ON_ERROR);
            return $this->sql->query("INSERT INTO `newsongs`(`ID`, `Songname`, `Songinfo`, `Songfile`) VALUES (null,'$name','$infos','$file')");
        } catch (\JsonException $e) {
        }
        return false;
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
        $id = $this->generateCommentID();
        try {
            $this->sql->query("INSERT INTO `charts`(`ID`, `SongIDs`, `Votes`, `ENDDate`, `StartDate`, `Active`)" .
                " VALUES ('$id','" . json_encode($songids, JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','$enddate','$startdate','0')");
            return $id;
        } catch (\JsonException $e) {
            return -1;
        }
    }

    public function get(): array
    {
        $sql = $this->id === 0 ? "SELECT * FROM `charts`" : "SELECT * FROM `charts` WHERE `ID`='$this->id'";
        $result = $this->sql->result($sql);
        $a = array();
        if ($this->id === 0) {
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
            }
        } else {
            foreach ($result as $row) {
                $a["id"] = $row["ID"];
                try {
                    $a["songid"] = json_decode($row["SongIDs"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                try {
                    $a["votes"] = json_decode($row["Votes"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a["enddate"] = $row["ENDDate"];
                $a["startdate"] = $row["StartDate"];
                $a["active"] = (bool)$row["Active"];
            }
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
        $infos = $this->get();
        $votes = $infos["votes"] ?? array();

        if (array_key_exists($userid, $votes)) {
            if (count($votes[$userid]) > 2) {
                return false;
            }
            if (array_key_exists($songid, $votes[$userid])) {
                return false;
            }
        }
        $votes[$userid][$songid] = $amount;
        if ($this->getStart() <= 0 && $this->getEnd() > 0) {
            try {
                $this->sql->query("UPDATE `charts` SET `Votes`='" . json_encode($votes, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$this->id'");
                return true;
            } catch (\JsonException $e) {
            }
        }
        return false;
    }

    public function changeActive(): bool
    {
        if (!empty($this->get())) {
            return $this->sql->query("UPDATE `charts` SET `Active`='" . !(bool)$this->get()["active"] . "' WHERE `ID`='$this->id'");
        }
        return false;
    }

    public function getEnd(): int
    {
        $date1 = date_create_from_format('Y-m-d', $this->get()["enddate"]);
        $date2 = date_create_from_format('Y-m-d', date('Y-m-d'));
        return ((array)date_diff($date1, $date2))["days"];
    }

    public function getStart(): int
    {
        $date1 = date_create_from_format('Y-m-d', $this->get()["startdate"]);
        $date2 = date_create_from_format('Y-m-d', date('Y-m-d'));
        return ((array)date_diff($date1, $date2))["days"];
    }

    public function getTopSongs(): array
    {
        $ar = array();
        foreach ($this->get()["songid"] as $key => $value) {
            $ar[$value] = 0;
        }
        $cl = $this->get()["votes"];
        foreach ($cl as $key => $value) {
            $num = ($ar[$value]) ?? 0;
            $num += $value;
            $ar[$value] = $value;
        }
        return $ar;
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

