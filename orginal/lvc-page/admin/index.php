<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$form = new Form();
$main = new Main();

$page = $_GET['page'] ?? "";
$main = new Main();

switch($page){
    case "addsong":{
        $form = new Form();
        $returntype = $_GET['status'] ?? "";

        switch($returntype){
            case "error":{
                $returnerror = intval($_GET['error']) ?? 0;
                $form->addTitle("New Song ERROR");
                switch($returnerror){
                    case 1001:
                        $form->addText("Wrong Data Type you can upload only: .mp3, .wav, .wma, .aac .ogg");
                        break;
                    case 1002:
                        $form->addText("Data Move Error!<br>Please try again later.");
                        break;
                    default:
                        $form->addText("Something weened wrong.<br>Please try again later.");
                        break;
                }
                echo($form->show());
                break;
            }
            case "success":{
                $form->addTitle("Song SUCCESS");
                $form->addText("The Song has been Uploaded Success.<br>The Song has the ID: ".($_GET["id"]??"NONE"));
                echo($form->show());
                break;
            }
            default:{
                $form->addTitle("Upload Song");
                $form->addInput("Song Name","","songname","",true);
                $form->addInput("Song Author","","songauthor","",true);
                $form->addUpload("Song Datei","New Data","songdata",".mp3 .wav .aac .wma .ogg",true);
                $form->addTextarea("Song Infos","","songinfo","",true);
                $form->addButton("Hinzufügen", "button", "songadd");

                echo($form->show());
                break;
            }
        }
        break;
    }

    case "av":{
        $form = new Form();
        break;
    }

    case "ev":{
        $form = new Form();
        break;
    }

    default:{

        break;
    }
}