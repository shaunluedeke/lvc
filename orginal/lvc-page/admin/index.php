<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$form = new Form();
$main = new Main();

$page = $_GET['page'] ?? "";
$main = new Main();

switch ($page) {
    case "addsong":
    {
        $form = new Form();
        $returntype = $_GET['status'] ?? "";

        switch ($returntype) {
            case "error":
            {
                $returnerror = (int)($_GET['error'] ?? 0);
                $form->addTitle("New Song ERROR");
                switch ($returnerror) {
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
            case "success":
            {
                $form->addTitle("Song SUCCESS");
                $form->addText("The Song has been Uploaded Success.<br>The Song has the ID: " . ($_GET["id"] ?? "NONE"));
                echo($form->show());
                break;
            }
            default:
            {
                $form->addTitle("Upload Song");
                $form->addInput("Song Name", "", "songname", "", true);
                $form->addInput("Song Author", "", "songauthor", "", true);
                $form->addUpload("Song Datei", "New Data", "songdata", "audio/mp3,audio/wav,audio/aac,audio/wma,audio/ogg", true);
                $form->addTextarea("Song Infos", "", "songinfo", "", true);
                $form->addButton("Hinzufügen", "button", "adminsongadd");

                echo($form->show());
                break;
            }
        }
        break;
    }

    case "songedit":
    {
        $id = $_GET['id'] ?? 0;
        if ($id === 0) {
            $form->addTitle("Song Status");
            $form->addText("Bitte gebe eine Song ID ein.");
            $form->addNumber("Song ID", "", "id", 0, 1);
            $form->addButton("Abfragen", "button", "adminstatussong");
            echo($form->show());
        } else {
            $song = $main->getSong($id)->get();
            $form->addTitle("Song Status");

            if (count($song) > 0) {
                $form->addNumber("Song ID", "", "id", $id, 0, 0, 1000000000, true, true);
                $form->addInput("Song Name", "", "name", $song["name"], true);
                $form->addInput("Song Author", "", "author", $song["info"]["author"], true);
                $form->addTextarea("Song Info", "", "infotxt", $song["info"]["infotxt"]);
                $form->addCheck("Song Status", "", "Active", "Deactive", "status", false);
                $form->setCheck($song["active"]);
                $form->addButton("Ändern", "button", "adminsongedit");
                echo($form->show());
            } else {
                $form->addText("Song ID: " . $id);
                $form->addText("Song nicht gefunden.");
                echo($form->show());
                $form = new Form();
                $form->addTitle("Song Status");
                $form->addText("Bitte gebe eine Song ID ein.");
                $form->addNumber("Song ID", "", "id", 0, true);
                $form->addButton("Abfragen", "button", "adminstatussong");
            }
        }

        break;
    }

    case "av":
    {
        $form = new Form();
        $id = $_GET["id"] ?? 0;
        $action = $_GET["action"] ?? "";

        if ($action === "switchactive") {
            $main->getChart($id)->changeActive();
            header("Location: index.php?admin&page=av");
        }

        if (($_GET["status"] ?? "") === "error") {
            $form->addText("Something wend wrong!");
            echo($form->show());
        }

        $cl = $main->getChart($id)->get();
        if (count($cl) < 1 || ($action === "add")) {
            $form->addTitle("Neue Abstimmungs Hinzufügen");
            $form->addInput("Song IDs", "Trennung mit ,", "songids", "", true);
            $form->addCalender("Start Datum", "", "startdate");
            $form->addCalender("Ende Datum", "", "enddate");
            $form->addButton("Hinzufügen", "button", "avadd");
            echo($form->show());
        }

        if (count($cl) === 1) {
            try {
                foreach ($cl as $key => $value) {
                    $id = $id !== 0 ? $id : $value["id"];
                }
            }catch (Exception $e){ }

            $form->addTitle("Abstimmungs Verwaltung für ID: " . $id);
            $btn = $cl[$id]["active"] ? '<a href="index.php?admin&page=av&id=' . $id . '&action=switchactive" class="btn btn-warning">Deaktivieren</a>' :
                '<a class="btn btn-success" href="index.php?admin&page=av&id=' . $id . '&action=switchactive">Activieren</a>';
            $html = 'Bei dieser Abstimmung haben zum jetzigen Zeitpunkt <b>' . count($cl[$id]["votes"]) . '</b> User abgestimmt.<br>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                ' . $btn . '<br><br>Jetziger Stand der umfrage <br><br>';

            $topsongs = ($main->getChart($id)->getTopSongs());
            arsort($topsongs);
            $html .= '<table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Platzierung</th>
                          <th scope="col">ID</th>
                          <th scope="col">Name</th>
                          <th scope="col">Votes</th>
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                    ';
            $platz = 1;
            foreach ($topsongs as $key => $value) {
                $songinfos = $main->getSong($key)->get();
                $html .= '
                 <tr>
                  <th scope="row">Platz ' . $platz . '</th>
                  <td>' . $key . '</td>
                  <td>' . $songinfos["name"] . '</td>
                  <td>' . $value . '</td>
                  <td><a class="btn btn-primary" href="index.php?song&id=' . $key . '">Anhören</a></td>
                </tr>
                ';
                $platz++;
            }
            $html .= '</tbody></table><br><br><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';

            $form->addText($html);


            echo($form->show());
        }

        if (count($cl) > 1) {
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
            foreach ($main->getChart()->get() as $key => $value) {
                $html .= '
                         <tr>
                          <th scope="row">' . $value["id"] . '</th>
                          <td><a class="btn btn-primary" href="index.php?admin&page=av&id=' . $value["id"] . '">Aufrufen</a></td>
                        </tr>
                        ';
            }
            $html .= '</tbody></table><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';
            echo($form->show());
        }

        break;
    }

    default:
    {
        $form = new Form();
        $form->addTitle("Admin Site | Abstimmung");
        $form->addText("<a href='index.php?admin&page=av' class='btn btn-primary'>Abstimmung verwalten</a>");
        echo($form->show());
        $form = new Form();
        $form->addTitle("Admin Site | Songs");
        $form->addText("<a href='index.php?admin&page=addsong' class='btm btn-primary'>Song hinzufügen</a><br><br>
                            <a href='index.php?admin&page=songedit' class='btm btn-primary'>Song ändern</a>");
        echo($form->show());
        break;
    }
}