<?php

require(__DIR__ . "/../config/discord_auth_config.php");
class discord_auth
{

    private $client_id;
    private $client_secret;
    private $redirect;

    private $tokenURL = 'https://discordapp.com/api/oauth2/token';
    private $apiURLBase = 'https://discordapp.com/api/users/@me';

    function __construct ($redirect="",$client_id="",$client_secret="") {
        $this -> client_id = $client_id===""?discord_auth_config::$client_id:$client_id;
        $this -> client_secret = $client_secret===""?discord_auth_config::$client_secret:$client_secret;
        $this -> redirect = $redirect===""?discord_auth_config::$redirect:$redirect;
    }


    public function login () {
        header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query(array(
                "client_id" => $this -> client_id,
                "redirect_uri" => $this -> redirect,
                "response_type" => 'code',
                "scope" => discord_auth_config::$scope)));
        die();
    }

    public function get_token ($code) {
        $ch = curl_init($this -> tokenURL);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            "grant_type" => "authorization_code",
            "client_id" => $this -> client_id,
            "client_secret" => $this -> client_secret,
            "redirect_uri" => $this -> redirect,
            "code" => $code)));
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        return json_decode($response);
    }

    public function get_info ($access_token) {
        $ch = curl_init($this -> apiURLBase);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        return json_decode($response);
    }

    public function getGuilds($access_token)
    {
        $ch = curl_init('https://discord.com/api/users/@me/guilds');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array('Accept: application/json');
        if (isset($_SESSION['access_token'])) {
            $headers[] = 'Authorization: Bearer ' . $access_token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        return json_decode($response, true);
    }

}
class discord
{
    private $info;
    function __construct ($info) {
        $this -> info = $info;
        if($info===null){
        }
    }
    public function getID():string{
        $info = $this->info;
        return $info->id;
    }
    public function getDiscrimination():string{
        $info = $this->info;
        return $info->discriminator;
    }
    public function getName():string{
        $info = $this->info;
        return $info->username;
    }
    public function getNameComplite(): string
    {
        $info = $this->info;
        return $info->username."#".$info->discriminator;
    }
    public function getEmail(): string
    {
        $info = $this->info;
        return $info->email;
    }
    public function getVerified(): bool
    {
        $info = $this->info;
        return $info->verified;
    }
    public function getLocation(): string
    {
        $info = $this->info;
        return $info->locale;
    }

    public function getBannercolor(): string
    {
        $info = $this->info;
        return $info->banner_color;
    }

    public function getBanner(): string
    {
        $info = $this->info;
        $avatar = $info->banner;
        if($avatar==null || $avatar ==" "|| $avatar ==""){
            return  $this->getBannercolor();
        }
        $ext = substr($avatar, 0, 2);
        if ($ext == "a_") {$avatar.= ".gif";} else {$avatar.= ".png";}

        return "https://cdn.discordapp.com/banner/".$info->id."/".$avatar;
    }

    public function getAvatar(): string
    {
        $info = $this->info;
        $avatar = $info->avatar;

        $ext = substr($avatar, 0, 2);
        if ($ext == "a_") {$avatar.= ".gif";} else {$avatar.= ".png";}

        return "https://cdn.discordapp.com/avatars/".$this->info->id."/".$avatar;
    }

    public function get():array{
        $a=array();
        $a["name"] = $this->getName();
        $a["email"] = $this->getEmail();
        $a["location"] = $this->getLocation();
        $a["avatar"] = $this->getAvatar();
        $a["isverified"] = $this->getVerified();
        return $a;
    }
}
class discord_server{
    private $guild;

    public function __construct($guild){$this->guild=$guild;}

    public function getName($server=0):string{
        return $this->guild["".$server]["name"];
    }

    public function getID($server=0):string{
        return $this->guild["".$server]["id"];
    }

    public function getIcon($server=0):string{
        $avatar = $this->guild["".$server]["icon"];
        if($avatar!=null&&$server!=""&&$server!=" ") {
            $ext = substr($avatar, 0, 2);
            if ($ext == "a_") {
                $avatar .= ".gif";
            } else {
                $avatar .= ".png";
            }
            return "https://cdn.discordapp.com/icons/" . $this->getID($server) . "/$avatar?size=256";
        }else{
            return "assets/img/none.png";
        }
    }

    public function getPermissions($server=0):int{
        return $this->guild["".$server]["permissions"];
    }

    public function getALL(bool $all=true,$server=0):array{
        if($all){
            $a=array();
            foreach(array_keys($this->guild) as $key){
                $a[$key]["name"]=$this->getName($key);
                $a[$key]["id"]=$this->getID($key);
                $a[$key]["icon"]=$this->getIcon($key);
                $a[$key]["permission"]=$this->getPermissions($server);
                $a[$key]["isowner"]=$this->hasPermission($key);
            }
            return $a;
        }else{
            $a=array();
            $a["name"]=$this->getName($server);
            $a["id"]=$this->getID($server);
            $a["icon"]=$this->getIcon($server);
            $a["permission"]=$this->getPermissions($server);
            $a["isowner"]=$this->hasPermission($server);
            return $a;
        }
    }

    public function hasPermission($server=0):bool{
        return ($this->getPermissions($server) & 2146958591) === 2146958591;
    }

}