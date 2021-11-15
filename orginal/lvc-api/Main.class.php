<?php

namespace wcf\system\lvc;

class Main{
    use wcf\system\lvc\SQL;

    private SQL $sql;

    public function __construct()
    {
        $this->sql = new SQL();
    }

    public function init():void{
            $this->sql->query("CREATE TABLE IF NOT EXISTS `history` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Date` VARCHAR(200) NOT NULL , `History` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            $this->sql->query("CREATE TABLE IF NOT EXISTS `songs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , `Comments` TEXT NOT NULL , `Upvotes` INT(16) NOT NULL , `Downvotes` INT(16) NOT NULL , `Active` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            $this->sql->query("CREATE TABLE IF NOT EXISTS `newsongs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            $this->sql->query("CREATE TABLE IF NOT EXISTS `charts` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `SongIDs` TEXT NOT NULL , `Votes` TEXT NOT NULL , `ENDDate` VARCHAR(200) NOT NULL , `StartDate` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
     }

#region History
    public function getHistory($date="all"):array{
        $sql = "SELECT `History` FROM `history` WHERE `Date`='$date'";
        if($date === "all"){
            $sql = "SELECT `Date`,`History` FROM `history`";
        }
        $result = $this->sql->result($sql);
        $a = array();
        foreach (($result) as $row){
            if(!isset($row["Date"])){
                try {return json_decode($row["History"], true, 512, JSON_THROW_ON_ERROR);} catch (JsonException $e) { }
            }
                try {$a[$row["Date"]] = json_decode($row["History"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
        }
        return $a;
    }

    /** @throws \JsonException */
    public function addHistory($data, $date=""):void{
        $d = $date!==""? $date : date("d.m.Y");
        $a = !is_array($data) ? $data :$this->getHistory($d);
        if(!is_array($data)) {
            $a[]= $data;
        }
        if(empty($this->getHistory($d))){
            $this->sql->query("INSERT INTO `history`(`ID`, `Date`, `History`) VALUES (null,'$d','". json_encode($a, JSON_THROW_ON_ERROR) ."')");
        }else{
            $this->sql->query("UPDATE `history` SET `History`='". json_encode($a, JSON_THROW_ON_ERROR) ."' WHERE `Date`='".$d."'");
        }
    }

#endregion

#region Song

    #region Info
        public function getSong(int $id=0):array{
            $sql = $id===0 ? "SELECT * FROM `songs`": "SELECT * FROM `songs` WHERE `ID`='$id'";
            $result = $this->sql->result($sql);
            $a = array();
            if($id===0) {
                foreach ($result as $row) {
                    $a[$row["ID"]]["id"] = $row["ID"];
                    $a[$row["ID"]]["name"] = $row["Songname"];
                    try {$a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
                    $a[$row["ID"]]["file"] = $row["Songfile"];
                    try {$a[$row["ID"]]["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
                    $a[$row["ID"]]["upvotes"] = (int)($row["Upvotes"]);
                    $a[$row["ID"]]["downvotes"] = (int)$row["Downvotes"];
                    $a[$row["ID"]]["active"] = (bool)$row["Active"];
                }
            }else{
                foreach ($result as $row) {
                    $a["id"] = $row["ID"];
                    $a["name"] = $row["Songname"];
                    try {$a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
                    $a["file"] = $row["Songfile"];
                    try {$a["comments"] = json_decode($row["Comments"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
                    $a["upvotes"] = (int)($row["Upvotes"]);
                    $a["downvotes"] = (int)$row["Downvotes"];
                    $a["active"] = (bool)$row["Active"];
                }
            }
            return $a;
        }

        public function addSong(string $name,array $info,string $file,bool $active):int{
            $id = $this->generateSongID();
            $this->sql->query("INSERT INTO `songs`(`ID`, `Songname`, `Songinfo`, `Songfile`, `Comments`, `Upvotes`, `Downvotes`, `Active`) VALUES".
                " ('$id','$name','$info','$file','[]','0','0','$active')");
            return $id;
        }

        public function removeSong(int $id):bool{
            if(empty($this->getSong($id))){ return false;}
            $this->sql->query("DELETE FROM `songs` WHERE `ID`='$id'");
            return empty($this->getSong($id));
        }

        public function updateActiveSong(int $id, bool $active){
            if(empty($this->getSong($id))){return false;}
            if($this->getSong($id)["active"]!==$active) {
                $this->sql->query("UPDATE `songs` SET `Active`='$active' WHERE `ID`='$id'");
            }
        }

    #endregion

    #region Song Comment
        public function addSongComment(int $id,$name,$comment){
            if(empty($this->getSong($id))){ return false;}
            $allcomments = $this->getSong($id)["comments"];
            $a = array();
            $a["name"]=$name;
            $a["comment"]=$comment;
            $a["time"]=date("H:M d.m.Y");
            $allcomments[$this->generateCommentID()]=$a;
            try {$this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$id'");} catch (\JsonException $e) {}
        }
        public function removeSongComment(int $id,$commentid){
            if(empty($this->getSong($id))){ return false;}
            $allcomments = $this->getSong($id)["comments"];
            if(!array_key_exists($commentid,$allcomments)){return false;}
            unset($allcomments[$commentid]);
            try {$this->sql->query("UPDATE `songs` SET `Comments`='" . json_encode($allcomments, JSON_THROW_ON_ERROR) . "' WHERE `ID`='$id'");} catch (\JsonException $e) {}
        }
    #endregion

    #region SongVote

        public function getVoting(int $id,$downvotes=false):int{
            return !$downvotes ? $this->getSong($id)["upvotes"] : $this->getSong($id)["downvotes"];
    }

        public function addVote(int $id,int $amount=1,bool $downvotes=false):void{
            $sql = !$downvotes?"UPDATE `songs` SET `Upvotes`='".($this->getVoting($id,$downvotes)+$amount)."' WHERE `ID`='$id'"
                :"UPDATE `songs` SET `Downvotes`='".($this->getVoting($id,$downvotes)+$amount)."' WHERE `ID`='$id'";
            $this->sql->query($sql);
        }

        public function removeVote(int $id,int $amount=1,bool $downvotes=false):void{
            $sql = !$downvotes?"UPDATE `songs` SET `Upvotes`='".($this->getVoting($id,$downvotes)-$amount)."' WHERE `ID`='$id'"
                :"UPDATE `songs` SET `Downvotes`='".($this->getVoting($id,$downvotes)-$amount)."' WHERE `ID`='$id'";
            $this->sql->query($sql);
        }

    #endregion

    #region GenerateID
    public function generateSongID():int{
        try {
            $i = random_int(0, 99999999);
            while (($this->sql->count("SELECT * FROM `songs` WHERE `ID`='$i'"))>0){
                $i= random_int(0, 99999999);
            }
            return $i;
        } catch (\Exception $e) {}
        return $this->generateSongID();
    }
    public function generateCommentID():int{
        try {
            return random_int(0, 999999999999);
        } catch (\Exception $e) {}
        return $this->generateSongID();
    }
    #endregion

#endregion

#region NewSong
    public function getNewSong(int $id = 0):array{
        $sql = $id===0 ? "SELECT * FROM `newsongs`": "SELECT * FROM `newsongs` WHERE `ID`='$id'";
        $result = $this->sql->result($sql);
        $a = array();
        if($id===0) {
            foreach (($result) as $row) {
                $a[$row["ID"]]["id"] = $row["ID"];
                $a[$row["ID"]]["name"] = $row["Songname"];
                try {$a[$row["ID"]]["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
                $a[$row["ID"]]["file"] = $row["Songfile"];
            }
        }else{
            foreach (($result) as $row) {
                $a["id"] = $row["ID"];
                $a["name"] = $row["Songname"];
                try {$a["info"] = json_decode($row["Songinfo"], true, 512, JSON_THROW_ON_ERROR);} catch (\JsonException $e) {}
                $a["file"] = $row["Songfile"];
            }
        }
        return $a;
    }

    public function addNewSong(string $name,array $info,string $file):bool{
        return $this->sql->query("INSERT INTO `newsongs`(`ID`, `Songname`, `Songinfo`, `Songfile`) VALUES (null,'$name','$info','$file')");
    }

    public function removeNewSong(int $id):bool{
        if(empty($this->getNewSong($id))){ return false;}
        $this->sql->query("DELETE FROM `newsongs` WHERE `ID`='$id'");
        return empty($this->getNewSong($id));
    }
#endregion

#region Charts



#endregion

}
