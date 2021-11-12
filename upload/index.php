<?php
session_start();
if(isset($_POST["pw"])){
    if($_POST["pw"]==="Spass009"){
        $_SESSION["login"]=true;
    }else{
        echo("wrong password");
    }

}
if(isset($_SESSION["login"]) && $_SESSION["login"]){
    if(isset($_FILES["userfile"])){
        if($_POST['type']==="api") {
            $uploadfile = '/var/www/html/lib/system/lvc/' . basename($_FILES['userfile']['name']);
            move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
        }
        if($_POST['type']==="form"){
            $handle = readText::getString($_FILES['userfile']['tmp_name']);
            $mysql = new mysql_connetion();
            $mysql->query("UPDATE `wcf1_page` SET `phpContent`='".$handle."' WHERE `pageID`='81'");
        }
        echo("ok");
    }
    ?>

    <form enctype="multipart/form-data" action="index.php" method="POST">
        <input name="userfile" type="file" />
        <label>
            <select name="type">
                <option value="api">API</option>
                <option value="form">Form</option>
            </select>
        </label>
        <input type="submit" value="Send File" />
    </form>

    <?php
}else{
    echo("<form action='index.php' method='post'><input type='password' name='pw' placeholder='Password'><input type='submit' value='Send File' /></form>");
}

class mysql_connetion
{
    private static $link = null;
    public function connect(): bool{

        if(self::$link===null) {
            self::$link = mysqli_connect("localhost", "root", "Spass009", "wcf", 3306);
            if (mysqli_connect_errno()) {
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }
    public function getLink(){
        if(self::$link===null){
            self::connect();
        }
        return self::$link;
    }
    public function disconnect(): bool {
        if(self::$link!==null) {
            mysqli_close(self::$link);
            return true;
        }else{
            return false;
        }
    }
    public function query($query): bool{
        if(self::$link===null){
            self::connect();
        }
        if(self::$link!==null) {
            if(mysqli_query(self::$link, $query)===TRUE){
                return true;
            }else {
                return false;
            }
        }
        return false;
    }
    public function result($query){
        if(self::$link===null){
            self::connect();
        }
        if(self::$link!==null) {
            if($result = mysqli_query(self::$link, $query)){
                return $result;
            }else {
                return null;
            }
        }
    }
    public function count($query):int{
        if(self::$link===null){
            self::connect();
        }
        if(self::$link!==null) {
            if($result = mysqli_query(self::$link, $query)){
                return mysqli_num_rows($result);
            }else {
                return 0;
            }
        }
        return 0;
    }
}
class readText
{

    public static function getString($file,$lineofset=0)
    {
        $txt = "";
        $txtarray = self::getArray($file);
        if($txtarray===null){
            return null;
        }
        for ($i = $lineofset, $iMax = count($txtarray); $i< $iMax; $i++){
            $txt.= $txtarray[$i];
        }
        return $txt;

    }

    public static function getArray($file)
    {
        $txt = array();
        if (file_exists( $file)) {
            if ($fileman = fopen($file, "r")) {
                while (!feof($fileman)) {
                    $txt[]=fgets($fileman);
                }
            }
            return $txt;
        }
        return null;
    }


}

?>