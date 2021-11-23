<?php

namespace wcf\system\lvc;
use wcf\system\lvc\SQL;

class Main
{


    private SQL $sql;

    public function __construct()
    {
        $this->sql = new SQL();
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

#region History
    public function getHistory($date = "all"): array
    {
        $sql = "SELECT `History` FROM `history` WHERE `Date`='$date'";
        if ($date === "all") {
            $sql = "SELECT `Date`,`History` FROM `history`";
        }
        $result = $this->sql->result($sql);
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

    /** @throws \JsonException */
    public function addHistory($data, $date = ""): void
    {
        $d = $date !== "" ? $date : date("d.m.Y");
        $a = !is_array($data) ? $data : $this->getHistory($d);
        if (!is_array($data)) {
            $a[] = $data;
        }
        if (empty($this->getHistory($d))) {
            $this->sql->query("INSERT INTO `history`(`ID`, `Date`, `History`) VALUES (null,'$d','" . json_encode($a, JSON_THROW_ON_ERROR) . "')");
        } else {
            $this->sql->query("UPDATE `history` SET `History`='" . json_encode($a, JSON_THROW_ON_ERROR) . "' WHERE `Date`='" . $d . "'");
        }
    }

#endregion

#region Song

    #region Info
    public function getSong(int $id = 0): array
    {
        $sql = $id === 0 ? "SELECT * FROM `songs`" : "SELECT * FROM `songs` WHERE `ID`='$id'";
        $result = $this->sql->result($sql);
        $a = array();
        if ($id === 0) {
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

    public function addSong(string $name, array $info, string $file): int
    {
        $id = $this->generateSongID();
        $file = "http://lvcharts.de/songdata/" . $id . "-" . $file;
        $this->sql->query("INSERT INTO `songs`(`ID`, `Songname`, `Songinfo`, `Songfile`, `Comments`, `Upvotes`, `Downvotes`, `Active`) VALUES" .
            " ('$id','$name','$info','$file','[]','0','0','true')");
        return $id;
    }

    public function removeSong(int $id): bool
    {
        if (empty($this->getSong($id))) {
            return false;
        }
        $this->addLog($id);
        $this->sql->query("DELETE FROM `songs` WHERE `ID`='$id'");
        return empty($this->getSong($id));
    }

    public function updateActiveSong(int $id, bool $active)
    {
        if (empty($this->getSong($id))) {
            return false;
        }
        if ($this->getSong($id)["active"] !== $active) {
            $this->sql->query("UPDATE `songs` SET `Active`='$active' WHERE `ID`='$id'");
        }
    }

    #endregion

    #region Song Comment
    public function addSongComment(int $id, $name, $comment)
    {
        if (empty($this->getSong($id))) {
            return false;
        }
        $allcomments = $this->getSong($id)["comments"];
        $a = array();
        $a["name"] = $name;
        $a["comment"] = $comment;
        $a["time"] = date("H:M um d.m.Y");
        $allcomments[$this->generateCommentID()] = $a;
        try {
            $this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$id'");
        } catch (\JsonException $e) {
        }
    }

    public function removeSongComment(int $id, $commentid)
    {
        if (empty($this->getSong($id))) {
            return false;
        }
        $allcomments = $this->getSong($id)["comments"];
        if (!array_key_exists($commentid, $allcomments)) {
            return false;
        }
        unset($allcomments[$commentid]);
        try {
            $this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$id'");
        } catch (\JsonException $e) {
        }
    }
    #endregion

    #region SongVote

    public function getSongVoting(int $id, $downvotes = false): int
    {
        return !$downvotes ? $this->getSong($id)["upvotes"] : $this->getSong($id)["downvotes"];
    }

    public function addSongVote(int $id, int $amount = 1, bool $downvotes = false): void
    {
        $sql = !$downvotes ? "UPDATE `songs` SET `Upvotes`='" . ($this->getSongVoting($id, $downvotes) + $amount) . "' WHERE `ID`='$id'"
            : "UPDATE `songs` SET `Downvotes`='" . ($this->getSongVoting($id, $downvotes) + $amount) . "' WHERE `ID`='$id'";
        $this->sql->query($sql);
    }

    public function removeSongVote(int $id, int $amount = 1, bool $downvotes = false): void
    {
        $sql = !$downvotes ? "UPDATE `songs` SET `Upvotes`='" . ($this->getSongVoting($id, $downvotes) - $amount) . "' WHERE `ID`='$id'"
            : "UPDATE `songs` SET `Downvotes`='" . ($this->getSongVoting($id, $downvotes) - $amount) . "' WHERE `ID`='$id'";
        $this->sql->query($sql);
    }

    public function getTopSongs(int $limit = -1, int $offset = -1, bool $downvotes = false): array
    {
        $limitstring = $limit === -1 ? "" : " LIMIT " . $limit;
        $offsetstring = $offset === -1 ? "" : " OFFSET " . $offset;
        $sql = !$downvotes ? "SELECT * FROM `songs` ORDER BY `Upvotes` ASC" . $limitstring . $offsetstring : "SELECT * FROM `songs` ORDER BY `Downvotes` ASC" . $limitstring . $offsetstring;
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
            return random_int(100, 999999999999);
        } catch (\Exception $e) {
        }
        return $this->generateSongID();
    }

    #endregion

#endregion

#region NewSong
    public function getNewSong(int $id = 0): array
    {
        $sql = $id === 0 ? "SELECT * FROM `newsongs`" : "SELECT * FROM `newsongs` WHERE `ID`='$id'";
        $result = $this->sql->result($sql);
        $a = array();
        if ($id === 0) {
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

    public function addNewSong(string $name, array $info, string $file): bool
    {
        try {
            $infos = json_encode($info, JSON_THROW_ON_ERROR);
            return $this->sql->query("INSERT INTO `newsongs`(`ID`, `Songname`, `Songinfo`, `Songfile`) VALUES (null,'$name','$infos','$file')");
        }catch (\JsonException $e){}
        return false;
    }

    public function removeNewSong(int $id): bool
    {
        if (empty($this->getNewSong($id))) {
            return false;
        }
        $this->sql->query("DELETE FROM `newsongs` WHERE `ID`='$id'");
        return empty($this->getNewSong($id));
    }

    public function acceptNewSong(int $id): bool
    {
        $info = $this->getNewSong($id);
        if (empty($info)) {
            return false;
        }
        $file = pathinfo(file($info["file"]))['filename'];
        $newid = $this->addSong($info["name"], $info["info"], $file);
        rename("/var/www/html/songdate/new/" . $file, "/var/www/html/songdate/" . $newid . "-" . $file);
        $this->addLog($id, true);
        $this->removeNewSong($id);
        return false;
    }
#endregion

#region Charts

    public function createCharts(string $startdate, string $enddate, array $songids): int
    {
        $id = $this->generateCommentID();
        try {
            $this->sql->query("INSERT INTO `charts`(`ID`, `SongIDs`, `Votes`, `ENDDate`, `StartDate`, `Active`)" .
                " VALUES ('$id','" . json_encode($songids, JSON_THROW_ON_ERROR) . "','" . json_encode(array(), JSON_THROW_ON_ERROR) . "','$enddate','$startdate','false')");
            return $id;
        } catch (\JsonException $e) {
            return -1;
        }
    }

    public function getCharts(int $id = 0): array
    {
        $sql = $id === 0 ? "SELECT * FROM `charts`" : "SELECT * FROM `charts` WHERE `ID`='$id'";
        $result = $this->sql->result($sql);
        $a = array();
        if ($id === 0) {
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
                $a[$row["ID"]]["active"] = $row["Active"];
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
                $a["active"] = $row["Active"];
            }
        }
        return $a;
    }

    public function deleteCharts(int $chartsid): bool
    {
        if (!empty($this->getCharts($chartsid))) {
            return $this->sql->query("DELETE FROM `charts` WHERE `ID`='$chartsid'");
        }
        return false;
    }


    public function addVote(int $chartsid, int $songid, int $userid, int $amount): bool
    {
        $infos = $this->getCharts($chartsid);
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
        if ($this->getChartsStart($chartsid) <= 0 && $this->getChartsEnd($chartsid) > 0) {
            try {
                $this->sql->query("UPDATE `charts` SET `Votes`='" . json_encode($votes, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$chartsid'");
                return true;
            } catch (\JsonException $e) {
            }
        }
        return false;
    }

    public function changeChartsActive($chartsid): bool
    {
        if (!empty($this->getCharts($chartsid))) {
            return $this->sql->query("UPDATE `charts` SET `Active`='" . !(bool)$this->getCharts($chartsid)["active"] . "' WHERE `ID`='$chartsid'");
        }
        return false;
    }

    public function getChartsEnd($chartsid): int
    {
        $date1 = date_create_from_format('Y-m-d', $this->getCharts($chartsid)["enddate"]);
        $date2 = date_create_from_format('Y-m-d', date('Y-m-d'));
        return ((array)date_diff($date1, $date2))["days"];
    }

    public function getChartsStart($chartsid): int
    {
        $date1 = date_create_from_format('Y-m-d', $this->getCharts($chartsid)["startdate"]);
        $date2 = date_create_from_format('Y-m-d', date('Y-m-d'));
        return ((array)date_diff($date1, $date2))["days"];
    }

    public function getTopSongsfromCharts($chartsid): array
    {
        $ar = array();
        foreach ($this->getCharts($chartsid)["songid"] as $key => $value) {
            $ar[$value] = 0;
        }
        $cl = $this->getCharts($chartsid)["votes"];
        foreach ($cl as $key => $value) {
            $num = ($ar[$value]) ?? 0;
            $num += $value;
            $ar[$value] = $value;
        }
        return $ar;
    }
#endregion

#region SongLogs

    public function getLog(bool $new = false, int $id = 0): array
    {
        $result = $this->sql->result(($id === 0 ? "SELECT * FROM `songlogs` WHERE `New`='$new'" : "SELECT * FROM `songlogs` WHERE `ID`='$id' AND `New`='$new'"));
        $a = array();
        if ($id === 0) {
            foreach ($result as $row) {
                $a[$row["ID"]]["id"] = $row["ID"];
                $a[$row["ID"]]["name"] = $row["Songname"];
                try {
                    $a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                }
                $a[$row["ID"]]["date"] = (int)($row["Date"]);
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
                $a["date"] = (int)($row["Date"]);
                $a["status"] = (bool)($row["New"]);
            }
        }
        return $a;
    }

    public function getAllLog(int $offset, int $limit, int $new = 2): array
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
            $a[$row["ID"]]["date"] = (int)($row["Date"]);
            $a[$row["ID"]]["status"] = (bool)($row["New"]);
        }
        return $a;
    }

    public function addLog(int $id, bool $new = false): bool
    {
        $infostring = "";
        $date = date("Y-m-d\TH:i");
        $i = $new ? $this->getNewSong($id) : $this->getSong($id);
        $name = $i["name"];
        try {
            $infostring = json_encode($i["info"], JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
        }
        return $this->sql->query("INSERT INTO `songlogs`(`ID`, `Songname`, `Songinfo`, `Date`, `New`) VALUES ('$id','$name','$infostring','$date','$new')");
    }

    public function removeLog(int $id): bool
    {
        if (empty($this->getSong($id))) {
            return false;
        }
        $this->sql->query("DELETE FROM `songlogs` WHERE `ID`='$id'");
        return empty($this->getSong($id));
    }

#endregion

#region Contest

    public function getContest(int $id = 0, bool $active = false): array
    {
        $ar = array();

        $sql = $id === 0 ? "SELECT * FROM `contest` WHERE `Activ`='$active'" : "SELECT * FROM `contest` WHERE `ID`='$id'";
        $result = $this->sql->result($sql);
        try {
            if ($id === 0) {
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
        }catch (\JsonException $e){}
        return $ar;
    }

    public function addContest(string $name,$startdate,$enddate):int{
        try {
            $i = random_int(1, 9999999);
            $this->sql->query("INSERT INTO `contest`(`ID`, `Name`, `Users`, `StartingDate`, `EndingDate`, `Activ`)".
                " VALUES ('$i','$name','". json_encode(array(), JSON_THROW_ON_ERROR) ."','$startdate','$enddate','true')");
            return $i;
        } catch (\Exception $e) {
        }
        return 0;
    }

    public function removeContest(int $id):bool{
        if(empty($this->getContest($id))){return false;}
        $this->sql->query("DELETE FROM `contest` WHERE `ID`='$id'");
        return empty($this->getContest($id));
    }

    public function updateContestStatus(int $id,bool $status=false):bool{
        return $this->sql->query("UPDATE `contest` SET `Activ`='$status' WHERE `ID`='$id'");
    }

    public function addContestUser(int $id,$user):bool{
        if(empty($this->getContest($id))){return false;}
        $users = $this->getContest($id)["user"];
        $users[] = $user;
        try {
            return $this->sql->query("UPDATE `contest` SET `Users`='". json_encode($users, JSON_THROW_ON_ERROR)."' WHERE `ID`='$id'");
        }catch (\JsonException $e){}
        return false;
    }
#endregion


}
