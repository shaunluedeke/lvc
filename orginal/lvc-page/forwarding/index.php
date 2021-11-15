<?PHP
if(!isset($_SERVER['HTTP_REFERER'])) {
    echo "error 1";
    return;
}


$path = explode("?", $_SERVER['HTTP_REFERER'])[1];
$path = explode("/", $path)[0];
$path = './custom/page/'.$path.'/forwarding.php';

if (!file_exists($path)) {
    echo "error 2 ".$path;
    return;
}
include $path;

use wcf\system\WCF;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\UserAction;

$wcf_user = WCF::getUser();
$wcf_user_id = $wcf_user->userID;

$HTTP_REFERER = "";

if(isset($_POST)) {
    $type = "";
    $data = array();
    foreach($_POST as $name => $value) {
        if($name=="HTTP_REFERER") {
            $HTTP_REFERER = $value;
        } else if($name == "button") {
            $value .= ";";
            $tmbdata = explode(";",$value);
            $type = $tmbdata[0];
            $data["button"] = $tmbdata[1];
        } else {
            $data[$name] = htmlentities($value);
            if(isset($_FILES)){
                $data["files"] = $_FILES;
            }
        }
    }
}

$forwarding = new Forwarding($wcf_user_id, $type, $data);

$HTTP_REFERER = $forwarding->getReferer($HTTP_REFERER);

if(method_exists ($forwarding, "isDebug")) {
    return;
}

if($HTTP_REFERER != "") {
    header("Location: " . $HTTP_REFERER);
} else if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

?>