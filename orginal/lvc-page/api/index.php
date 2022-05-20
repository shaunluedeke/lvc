<?php
require_once('./wls/global.php');
$ip = ($_SERVER['REMOTE_ADDR']) ?? "";
$method = $_SERVER['REQUEST_METHOD'] ?? "";
if ($ip === "" || $method === "") {
    header('HTTP/1.0 423 Locked');
}

use wcf\system\lvc\Main;

$main = new Main();
$main->init();
$api = $main->getApi($ip);

if(!$api->hasPermission(1)){$api->noPermission(1);header('HTTP/1.0 403 Forbidden'); return;}

$data = [];


switch($method){
    case "GET":{$data = $_GET; if(($data["action"] ?? "") === "login"){break;} if(!$api->hasPermission(1)){$api->noPermission(1);header('HTTP/1.0 403 Forbidden'); return;} break;}
    case "POST":{$data = $_POST; if(($data["action"] ?? "") === "login"){break;} if(!$api->hasPermission(1)){$api->noPermission(2);header('HTTP/1.0 403 Forbidden'); return;}break; }
    default:{header('HTTP/1.0 400 Bad Request'); return;}
}

header("Content-Type: application/json");

$type = $data['type'] ?? "";
$id = (int)($data['id'] ?? 0);

try {
    switch ($data["action"] ?? "") {

        case "song":
        {
            $song = $main->getSong($id);
            switch($type){
                case "get":{
                    if (count($song->get()) > 0) {
                        echo json_encode($song->get(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(["status" => "error", "error" => "Song not found"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    }
                    break;
                }

                case "gettop":{
                    if (count($song->getTopSongs()) > 0) {
                        echo json_encode($song->getTopSongs(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(["status" => "error", "error" => "Song not found"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    }
                    break;
                }

                default:{header('HTTP/1.0 400 Bad Request'); return;}

            }

            break;
        }

        case "newsong":{
            $song = $main->getNewSong($id);
            if (count($song->get()) > 0) {
                echo json_encode($song->get(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["status" => "error", "error" => "Song not found"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            }
            break;
        }

        case "charts":{
            $charts = $main->getChart($id);
            switch($type){
                case "get":{
                    if (count($charts->get()) > 0) {
                        echo json_encode($charts->get(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(["status" => "error", "error" => "Charts not found"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    }
                    break;
                }

                case "gettop":{
                    $charts->setId(0);
                    if (count($charts->getTopSongs()) > 0) {
                        echo json_encode($charts->getTopSongs(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(["status" => "error", "error" => "Charts not found"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    }
                    break;
                }

                case "getVotesfromUser":{
                    if (count($charts->getVotesFromUser($data["userid"])) > 0) {
                        echo json_encode($charts->getVotesFromUser($data["userid"]), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    } else {
                        echo json_encode(["status" => "error", "error" => "Charts not found"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    }
                    break;
                }

                default:{header('HTTP/1.0 400 Bad Request'); return;}
            }
            break;
        }

        case "login":{
            $username = $data["username"] ?? "";
            $password = base64_decode($data["password"]) ?? "";
            try {
                $user = wcf\system\user\authentication\UserAuthenticationFactory::getInstance()->getUserAuthentication()->loginManually($username, $password);
                echo json_encode(["status" => "success", "userid" => $user->userID], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                return;
            }catch (Exception $e){
                if($e->getField() === "username"){
                    try{
                        $user = wcf\system\user\authentication\EmailUserAuthentication::getInstance()->getUserAuthentication()->loginManually($username, $password);
                        echo json_encode(["status" => "success", "userid" => $user->userID], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                        return;
                    }catch (Exception $e){
                        echo json_encode(["status" => "error", "error" => "username"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                        return;
                    }
                }
                echo json_encode(["status" => "error", "error" => "password"], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        default:
        {
            header('HTTP/1.0 400 Bad Request');
            return;
        }

    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    return;
}
return;
