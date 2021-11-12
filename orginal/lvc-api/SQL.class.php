<?php

namespace wcf\system\lvc;
use wcf\system\WCF;

class SQL{

    private $sql;

    function __construct(){$this->sql = WCF::getDB(); }

    public function query($sql){
        $state = $this->sql->prepareStatement($sql);
        return $state->execute();

    }
    public function result($sql):array{
        $state = $this->sql->prepareStatement($sql);
        return $state->fetchArray();
    }
    public function count($sql):int{
        return count($this->result($sql));
    }
}