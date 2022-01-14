<?PHP

use wcf\system\lvc\Forwarding;

$forward = new Forwarding();
if (!isset($_SERVER['HTTP_REFERER'])) {
    echo "error 1";
    return;
}
use wcf\system\WCF;
$user = WCF::getUser();
$HTTP_REFERER = "";

$type = "";
$data = array();
if (isset($_POST)) {
    foreach ($_POST as $name => $value) {
        if ($name === "HTTP_REFERER") {
            $HTTP_REFERER = $value;
        } else if ($name === "button") {
            $value .= ";";
            $tmbdata = explode(";", $value);
            $type = $tmbdata[0];
            $data["button"] = $tmbdata[1];
        } else {
            $data[$name] = htmlentities($value);
            if (isset($_FILES)) {
                $data["files"] = $_FILES;
            }
        }
    }
}else{
    echo("Post not set");
}

$http_refere = "";
$id = 0;

if(strpos($type,"av/")){
    $type = "av";
    $id = ((int)explode("/", $type)[1]);
}

switch ($type) {

    case "songadd":
    {
        $http_refere = $forward->addSong($data);
        break;
    }
    case "addcomment":
    {
        $http_refere = $forward->addComment($data, $user->username);
        break;
    }
    case "songsearch":{
        $http_refere = $forward->search($data);
        break;
    }
    case "adminsongadd":
    {
        $http_refere = $forward->addAdminSong($data);
        break;
    }
    case "avadd":
    {
        $http_refere = $forward->addAV($data);
        break;
    }

    case "av":{
        $http_refere = $forward->setAV($id,$user->userID,$data);
        break;
    }

}
$debug = false;

if($debug){
    echo("<pre>");
    print_r($data,false);
    echo("</pre><br>Return value: ".$http_refere);
    echo("<br>".$type);
    return;
}

if ($http_refere !== "") {
    header("Location: " . $http_refere);
} else if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

?>