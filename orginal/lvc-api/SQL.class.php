<?php

namespace wcf\system\lvc;
use wcf\system\WCF;

class SQL{

    private $sql;

    public function __construct(){$this->sql = WCF::getDB(); }

    public function query($sql){
        $state = $this->sql->prepareStatement($sql);
        return $state->execute();

    }

    public function queryID($sql){
        $state = $this->sql->prepareStatement($sql);
        $state->execute();
        return $this->sql->lastInsertId();
    }

    public function result($sql){
        $state = $this->sql->prepareStatement($sql);
        $state->execute();
        return $state->fetchALL();
    }
    public function count($sql):int{
        return count($this->result($sql));
    }
}