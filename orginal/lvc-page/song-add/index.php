<?php
session_start();

use wcf\system\lvc\Form;
$form = new Form();

$returntype = $_GET['status'] ?? "";

switch ($returntype) {
    case "error":
    {
        $form = new Form();
        $returnerror = $_GET['error'] ?? 0;
        $form->addTitle("New Song ERROR");
        switch ($returnerror) {
            case "1001":
                $form->addText("Wrong Data Type you can upload only: .mp3, .wav, .wma, .aac .ogg");
                break;
            case "1002":
                $form->addText("Data Move Error!<br>Please try again later.");
                break;
            case "1003":
                $form->addText("Database Error!<br>Please try again later.");
                break;
            default:
                $form->addText("Something weened wrong.<br>Please try again later.");
                break;
        }
        echo($form->show());
        break;
    }
    case "success":
    {
        $form = new Form();
        $form->addTitle("New Song SUCCESS");
        $form->addText("The Song has been Uploaded Success.<br>A Team member will checked it later.");
        echo($form->show());
        break;
    }
    default:
    {
        $form->addTitle("New Song upload");
        $form->addInput("Song Name", "", "songname", "", true);
        $form->addInput("Song Author", "", "songauthor", "", true);
        $form->addUpload("Song Datei", "New Data", "songdata", "audio/mp3,audio/wav,audio/aac,audio/wma,audio/ogg", false);
        $form->addTextarea("Song Infos", "", "songinfo", "", true);
        $form->addButton("Hinzufügen","button", "songadd");

        echo($form->show());
        break;
    }
}

?>