<?PHP

use wcf\system\WCF;
use wcf\system\lvc\Forwarding;

$forward = new Forwarding();
if (!isset($_SERVER['HTTP_REFERER'])) {
    echo "error 1";
    return;
}

use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\UserAction;

$wcf_user = WCF::getUser();
$wcf_user_id = $wcf_user->userID;

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

switch ($type) {

    case "songadd":
    {
        echo("0.5");
        $http_refere = $forward->addSong($data);
        break;
    }
    case "addcomment":
    {
        $http_refere = $forward->addComment($data, $wcf_user->username);
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

}
$debug = true;

if($debug){
    echo("<pre>");
    print_r($data);
    echo("</pre><br>Return value: ".$http_refere);
    echo($type);
    return;
}

if ($http_refere !== "") {
    header("Location: " . $http_refere);
} else if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

?>