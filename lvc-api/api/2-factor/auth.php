<?php

class auth
{
    private $id;
    function __construct($id=0){
        $this->id = $id;
    }

    public function verify($key):bool{
        require_once(__DIR__ ."/../mysql/mysql_connetion.php");
        $mysql = new mysql_connetion();

        if($mysql->count("SELECT `ID` FROM `verify` WHERE `ID`='$this->id'")>0){

            $status = 0;
            $gs = "";
            $email = "";
            $discord = "";

            $result = ($mysql->result("SELECT `Google-Secret`, `2Factor`, `Email`, `Discord` FROM `verify` WHERE `ID`='$this->id'"));
            while($row = mysqli_fetch_array($result)){
                $status = (int) $row['2Factor'];
                $gs = unserialize(base64_decode(base64_decode($row['Google-Secret'])),["allowed_classes"]);
                $email = base64_decode($row['Email']);
                $discord = ($row['Discord']);
            }
            $edkey="";
            $edtype=0;
            if($status===1||$status===2){
                $result = ($mysql->result("SELECT * FROM `auth` WHERE `ID`='$this->id'"));
                while($row = mysqli_fetch_array($result)){
                    $edkey = (int) $row['Key'];
                    $edtype = (int) $row['Type'];
                }
            }

            switch($status){
                case 1: //Email
                    if($edtype===1){
                        if( $edkey===$key){
                            $mysql->query("DELETE FROM `auth` WHERE `ID`='$this->id'");
                            return true;
                        }else{
                            return false;
                        }
                    }
                    break;
                case 2: //Discord
                    if($edtype===2){
                        if( $edkey===$key){
                            $mysql->query("DELETE FROM `auth` WHERE `ID`='$this->id'");
                            return true;
                        }else{
                            return false;
                        }
                    }
                    break;
                case 3:
                    require_once(__DIR__."/googleLib/GoogleAuthenticator.php");
                    $ga = new GoogleAuthenticator();
                    return $ga->verifyCode($gs, $key,2);
                    break;
                case 0:
                    return true;
                    break;
            }
        }
        return false;
    }

    public function send(){

        require_once(__DIR__ ."/../mysql/mysql_connetion.php");
        $mysql = new mysql_connetion();

        require_once(__DIR__."/../random/random.php");
        $key = random::getInt(6, false);

        $status = 0;
        $email = "";

        $result = ($mysql->result("SELECT  `2Factor`, `Email`, `Discord` FROM `verify` WHERE `ID`='$this->id'"));
        while($row = mysqli_fetch_array($result)){
            $status = (int) $row['2Factor'];
            $email = base64_decode($row['Email']);
        }
        if($status>0&&$status<3) {
            if ($mysql->count("SELECT `ID` FROM `auth` WHERE `ID`='$this->id'") < 1) {
                $mysql->query("INSERT INTO `auth`(`ID`, `Key`, `Type`) VALUES ('$this->id','$key','$status')");
                if($status===1){
                    $txt='<div>
                    <h1>Hello.</h1>
                    <h1><br>Your Login Key is:<br></h1>
                    <h2> '.$key.'</h2>
                    <br>
                    </div>';
                    require_once(__DIR__.'/../mail/sendmail.php');
                    require_once(__DIR__.'/../../php/configs.php');
                    sendmail::send($email,"Login Key for ".configs::$title,$txt);
                }
            }else if($status===1){
                $txt='<div>
                <h1>Hello.</h1>
                <h1><br>Your Login Key is:<br></h1>
                <h2> '.$key.'</h2>
                <br>
                </div>';
                require_once(__DIR__.'/../mail/sendmail.php');
                require_once(__DIR__.'/../../php/configs.php');
                sendmail::send($email,"Login Key for ".configs::$title,$txt);
            }
        }

    }

    public static function generateSecret(){
        require_once(__DIR__."/googleLib/GoogleAuthenticator.php");

        $ga = new GoogleAuthenticator();
        $ga->createSecret(32);

        return base64_encode(base64_encode(serialize($ga)));
    }
}