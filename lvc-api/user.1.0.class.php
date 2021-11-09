<?php

class User_1_0{

    private $api;
    private $sql;

    public function __construct($api)
    {
        $this->api = $api;
        $this->sql = $api->getMySql("lvcdata");
    }

    public function init():bool{
        try{
            $this->sql->query("CREATE TABLE IF NOT EXISTS `history` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Date` VARCHAR(200) NOT NULL , `History` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            $this->sql->query("CREATE TABLE IF NOT EXISTS `songs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , `Comments` TEXT NOT NULL , `Upvotes` INT(16) NOT NULL , `Downvotes` INT(16) NOT NULL , `Active` BOOLEAN NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            $this->sql->query("CREATE TABLE IF NOT EXISTS `newsongs` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `Songname` VARCHAR(200) NOT NULL , `Songinfo` TEXT NOT NULL , `Songfile` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            $this->sql->query("CREATE TABLE IF NOT EXISTS `charts` ( `ID` INT(16) NOT NULL AUTO_INCREMENT , `SongIDs` TEXT NOT NULL , `Votes` TEXT NOT NULL , `ENDDate` VARCHAR(200) NOT NULL , `StartDate` VARCHAR(200) NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;");
            return true;
        }catch (Exception $e){}
        return false;
    }

#region History
    public function getHistory($date="all"):array{
        $sql = "SELECT `History` FROM `history` WHERE `Date`='$date'";
        if($date == "all"){
            $sql = "SELECT `Date`,`History` FROM `history`";
        }
        $result = $this->sql->query($sql);
        $a = array();
        foreach (mysqli_fetch_array($result) as $row){
            if(!isset($row["Date"])){
                return json_decode($row["History"]);
            }
            $a[$row["Date"]]= json_decode($row["History"]);
        }
        return $a;
    }

    public function addHistory($data,$date=""){
        $d = $date!=""? $date : date("d.m.Y");
        $a = is_array($data) ? $data :$this->getHistory($d);
        if(!is_array($data)) {
            $a[]= $data;
        }
        if(empty($this->getHistory($d))){
            $this->sql->query("INSERT INTO `history`(`ID`, `Date`, `History`) VALUES (null,'$d','".json_encode($a)."')");
        }else{
            $this->sql->query("UPDATE `history` SET `History`='".json_encode($a)."' WHERE `Date`='$d'");
        }
    }

#endregion

#region Song

    #region Info
        public function getSong(int $id=0):array{
            $sql = $id==0 ? "SELECT * FROM `songs`": "SELECT * FROM `songs` WHERE `ID`='$id'";
            $result = $this->sql->query($sql);
            $a = array();
            foreach (mysqli_fetch_array($result) as $row){
                $a["id"] = $row["ID"];
                $a["name"] = $row["Songname"];
                $a["info"] = json_decode($row["Songinfo"]);
                $a["file"] = $row["Songfile"];
                $a["comments"] = json_decode($row["Comments"]);
                $a["upvotes"] = intval($row["Upvotes"]);
                $a["downvotes"] = intval($row["Downvotes"]);
                $a["active"] = boolval($row["Active"]);
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
            if($this->getSong($id)["active"]!=$active) {
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
            $this->sql->query("UPDATE `songs` SET `Comments`='".json_encode($allcomments)."' WHERE `ID`='$id'");
        }
        public function removeSongComment(int $id,$commentid){
            if(empty($this->getSong($id))){ return false;}
            $allcomments = $this->getSong($id)["comments"];
            if(!array_key_exists($commentid,$allcomments)){return false;}
            unset($allcomments[$commentid]);
            $this->sql->query("UPDATE `songs` SET `Comments`='".json_encode($allcomments)."' WHERE `ID`='$id'");
        }
    #endregion

    #region SongVote

        public function getVoting(int $id,$downvotes=false):int{
            return !$downvotes ? $this->getSong($id)["upvotes"] : $this->getSong($id)["downvotes"];
    }

        public function addVote(int $id,int $amount=1,bool $downvotes=false){
            $sql = !$downvotes?"UPDATE `songs` SET `Upvotes`='".($this->getVoting($id,$downvotes)+$amount)."' WHERE `ID`='$id'"
                :"UPDATE `songs` SET `Downvotes`='".($this->getVoting($id,$downvotes)+$amount)."' WHERE `ID`='$id'";
            $this->sql->query($sql);
        }

        public function removeVote(int $id,int $amount=1,bool $downvotes=false){
            $sql = !$downvotes?"UPDATE `songs` SET `Upvotes`='".($this->getVoting($id,$downvotes)-$amount)."' WHERE `ID`='$id'"
                :"UPDATE `songs` SET `Downvotes`='".($this->getVoting($id,$downvotes)-$amount)."' WHERE `ID`='$id'";
            $this->sql->query($sql);
        }

    #endregion

    #region GenerateID
    public function generateSongID():int{
        try {
            $i = random_int(0, 99999999);
            while (mysqli_num_rows($this->sql->query("SELECT * FROM `songs` WHERE `ID`='$i'"))>0){
                $i= random_int(0, 99999999);
            }
            return $i;
        } catch (Exception $e) {}
        return $this->generateSongID();
    }
    public function generateCommentID():int{
        try {
            return random_int(0, 999999999999);
        } catch (Exception $e) {}
        return $this->generateSongID();
    }
    #endregion

#endregion



}
