<?php
class Forwarding {
    private $http_refere;
    private $main;
    function __construct($wcf_user_id, $type, $data) {
        global $api;
        $this->heads = $api->loadClass("Heads");
        if($type === "songadd"){
            if(isset($data["files"]) && isset($data["files"]["img"])) {
                $this->addUserHead($wcf_user_id, $data);
            }else{
                $this->http_refere = "./index.php?head-add/&status=error&error=1003";
            }
        }

    }

    function addUserHead($wcf_user_id,$data){
        if($this->heads->getPoints($wcf_user_id)>=100){
            if(($data["files"]["img"]["type"]=="audio/wav")||
                ($data["files"]["img"]["type"]=="audio/mp3")||
                ($data["files"]["img"]["type"]=="audio/wma")||
                ($data["files"]["img"]["type"]=="audio/aac")){
                $id = $this->heads->getID(-1);
                $name = $this->name_bereinigen($this->heads->sonderzeichenhinzufügen($data["name"]));
                if(!is_dir("heads/userheads/")) {
                    if (!mkdir("heads/userheads/", 0777, TRUE) && !is_dir("heads/userheads/")) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', "heads/userheads/"));
                    }
                }
                if(getimagesize($data["files"]["img"]['tmp_name'])[0]===64&&getimagesize($data["files"]["img"]['tmp_name'])[1]===64){
                    if(in_array($name,$this->heads->getNames(),true)) {
                        if (move_uploaded_file($data["files"]["img"]['tmp_name'], "heads/userheads/" . $id . "-" . $name . ".png")) {
                            if ($this->heads->addUserHead($id, $name, $data["categorie"], "heads/userheads/" . $id . "-" . $name . ".png",
                                $wcf_user_id, $data["crate"] === "Ja", $data["skydrop"] === "Ja")) {
                                $this->heads->removePoints($wcf_user_id, 100);
                                $this->http_refere = "./index.php?head-add/&status=success&id=" . $id;
                            } else {
                                $this->http_refere = "./index.php?head-add/&status=error&error=1005";
                            }
                        } else {
                            $this->http_refere = "./index.php?head-add/&status=error&error=1004";
                        }
                    }else{
                        $this->http_refere = "./index.php?head-add/&status=error&error=1003";
                    }
                }else{
                    $this->http_refere = "./index.php?head-add/&status=error&error=1002";
                }
            }else{
                $this->http_refere = "./index.php?head-add/&status=error&error=1001";
            }
        }else{
            $this->http_refere = "./index.php?head-add/&status=error&error=1000";
        }
    }

    function getReferer($http_refere) {
        return $this->http_refere;
    }

    function isDebug() {
        return true;
    }
}
?>