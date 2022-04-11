<?php

namespace wcf\system\lvc;

class Discord{

    private string $webhookurl;
    private string $txt="Das ist ein Test";
    private string $title="Discord Webhook";
    private string $avatar="";
    private string $username="LVCharts Webmanager";
    private string $hex="3366ff";
    private string $footer="";

    public function __construct(string $url="")
    {
        $this->webhookurl = $url===""?"https://discord.com/api/webhooks/963083711241392228/LnadkeGyDhNQfTvhxc1w1PUc-4UzNQpQd-JKH3O1ApsgIaGHKnVoKWSnL-FjPOTS2fH5":$url;
    }

    public function setTxt(string $txt): void
    {
        $this->txt = $txt;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setAvatar(string $title): void
    {
        $this->avatar = '"avatar_url" => '.$title;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setColor(string $hex): void
    {
        $this->hex = $hex;
    }

    public function setFooter(string $footer): void
    {
        $this->footer = $footer;
    }

    public function getData():string{
        $timestamp = date("c");

        try {
            return json_encode([

                // Username
                "username" => $this->username,


                "tts" => false,
                "embeds" => [
                    [
                        "title" => $this->title,
                        "type" => "rich",
                        "description" => $this->txt,
                        "timestamp" => $timestamp,
                        "color" => hexdec($this->hex),

                        // Footer
                        "footer" => [
                            "text" => $this->footer,
                        ],

                        // Image to send
                        "image" => [
                            "url" => $this->avatar
                        ],
                    ]
                ]

            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
        }
        return "";
    }

    public function send():void{
        $ch = curl_init($this->webhookurl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getData());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_exec($ch);
        curl_close($ch);
    }
}