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
                $returnerror = (int)($_GET['error'] ?? 0);
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
                $form->addUpload("Song Datei","New Data","songdata","audio/mp3,audio/wav,audio/aac,audio/wma,audio/ogg",true);
                $form->addTextarea("Song Infos","","songinfo","",true);
                $form->addButton("Hinzufügen", "button", "adminsongadd");

                echo($form->show());
                break;
            }
        }
        break;
    }

    case "av":{
        $form = new Form();
        $id = $_GET["id"] ?? 0;
        $action = $_GET["action"]??"";
        if($action==="switchactive"){
            $main->changeChartsActive($id);
        }
        if(($_GET["status"]??"") === "error"){
            $form->addText("Something wend wrong!");
            echo($form->show());
        }
        $cl=$main->getCharts($id);
        if(count($cl)===0||$action==="add"){
            $form->addTitle("Neue Abstimmungs Hinzufügen");
            $form->addInput("Song IDs","Trennung mit ,","songids","",true);
            $form->addCalender("Start Datum","","startdate");
            $form->addCalender("Ende Datum","","enddate");
            $form->addButton("Hinzufügen", "button", "avadd");
            echo($form->show());
        }

        if(count($cl)===1){
            $id = $id!==0?$id:$cl[0]["id"];

            $form->addTitle("Abstimmungs Verwaltung für ID: ".$id);
            $btn = $cl[$id]["active"] ? '<a href="index.php?admin&page=av&id='.$id.'&action=switchactive" class="btn btn-warning">Deaktivieren</a>' :
                                        '<a class="btn btn-success" href="index.php?admin&page=av&id='.$id.'&action=switchactive">Activieren</a>';
            $html = 'Bei dieser Abstimmung haben zum jetzigen Zeitpunkt <b>'.count($cl[$id]["votes"]).'</b> User abgestimmt.<br>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                '.$btn.'<br><br>Jetziger Stand der umfrage <br><br>';

            $topsongs = $main->getTopSongsfromCharts($id);
            $topsongs = arsort($topsongs);

            $html.= '<table class="table">
                      <thead>
                        <tr>
                          <th scope="col">ID</th>
                          <th scope="col">Name</th>
                          <th scope="col">Votes</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                    ';
            foreach($topsongs as $key=>$value){
                $songinfos = $main->getSong($key);
                $html .= '
                 <tr>
                  <th scope="row">'.$key.'</th>
                  <td>'.$songinfos["name"].'</td>
                  <td>'.$value.'</td>
                  <td><a class="btn btn-primary" href="index.php?song&id='.$key.'">Anhören</a></td>
                </tr>
                ';
            }
            $html.='</tbody></table><br><br><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';

            $form->addText($html);


            echo($form->show());
        }

        if(count($cl)>1){
            $form->addTitle("Abstimmungs Verwaltung");
            $html = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                    
                     <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">ID</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                    ';
            foreach($main->getCharts() as $key=>$value){
                $html .= '
                         <tr>
                          <th scope="row">'.$value["id"].'</th>
                          <td><a class="btn btn-primary" href="index.php?admin&page=av&id='.$value["id"].'">Aufrufen</a></td>
                        </tr>
                        ';
            }
            $html.='</tbody></table><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';
            echo($form->show());
        }

        break;
    }

    default:{
        $form = new Form();
        $form->addText("<a href='index.php?admin&page=av' class='btn-primary'>Abstimmung verwalten</a>
                             <a href='index.php?admin&page=addsong' class='btn-primary'>Song hinzufügen</a>");
        echo($form->show());
        break;
    }
}