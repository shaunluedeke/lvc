<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$form = new Form();
$main = new Main();

$page = $_GET['page'] ?? "";
$id = (int)($_GET["id"] ?? 0);
$main = new Main();

switch ($page) {
    case "newsongs":
    {
        $form = new Form();
        $action = $_GET["action"] ?? "";
        $newsongs = $main->getNewSong($id);
        if (count($newsongs->get()) < 1) {
            $form->addTitle("No new songs found");
            $form->addText("No new songs found<br><br><a href='index.php?admin'>Back</a>");
            $form->show();
        } else if ($id === 0 || count($newsongs->get()) > 1) {
            $html = '<table id="Table" class="table table-striped" data-toggle="table" data-pagination="true" data-search="true"><thead><tr>  <th scope="col" data-sortable="true" data-field="Akte">Name</th><th scope="col" data-sortable="true" data-field="name">Author</th> <th scope="col" data-sortable="true" data-field="port">Datum</th><th scope="col" data-field="date"></th></tr></thead><tbody> ';
            $a = $newsongs->get();
            foreach ($a as $key => $value) {
                if ($value["active"] === true) {
                    $html .= '<tr>
                        <th scope="row">' . Main::addSymbol($value["name"]) . '</th>
                         <td>' . Main::addSymbol($value["info"]["author"]) . '</td>
                         <td>' . $value["info"]["uploaddate"] . '</td>
                         <td><a href="index.php?admin/&page=newsong&id=' . $value["id"] . '">Anhören</a></td>
                    </tr>';
                }
            }
            $html .= '</tbody></table>';
            $form->addText($html);
            echo($form->show());
        } else {
            $info = $newsongs->get();
            $form->addTitle("Song: " . Main::addSymbol($info["name"]));
            $infotext = $info["info"]["infotxt"] === "" ? "" : '<li>Info Text: ' . Main::addSymbol($info["info"]["infotxt"]) . '</li>';
            $form->addText(
                '<audio controls><source src="' . $info["file"] . '" ></audio><br>
                   <p>Infos:</p><br><br>
                   <ul>
                      <li>Author: ' . Main::addSymbol($info["info"]["author"]) . '</li>
                      ' . $infotext . '
                      <li>Upload date: ' . $info["info"]["uploaddate"] . '</li>
                   </ul><br>
                   ');
            echo($form->show());
            $form = new Form();
            $form->addTitle("Controller:");
            $form->addNumber("ID", "Songid", "id", (int)$info["id"], 0, 0, 100000000000000, true, true);
            $form->addButton("Downloaden", "button", "adminnewsongdownload");
            $form->addButton("Löschen", "button", "adminnewsongdelete");
            $form->addButton("Hinzufügen", "button", "adminnewsongadd");
            $form->addText("<br><br><a href='index.php?admin'>Back</a>");
            echo($form->show());
        }

        break;
    }

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
            $form->addNumber("Song ID", "", "id", 0, 1, 0, 1000000000000000, true, false);
            $form->addButton("Abfragen", "button", "adminstatussong");
            echo($form->show());
        } else {
            $song = $main->getSong($id)->get();
            $form->addTitle("Song Status");

            if (count($song) > 0) {
                $form->addNumber("Song ID", "", "id", $id, 0, 0, 1000000000, true, true);
                $form->addInput("Song Name", "", "name", Main::addSymbol($song["name"]), true);
                $form->addInput("Song Author", "", "author", Main::addSymbol($song["info"]["author"]), true);
                $form->addTextarea("Song Info", "", "infotxt", Main::addSymbol($song["info"]["infotxt"]));
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
        $oldid = $id;
        $action = $_GET["action"] ?? "";

        if ($action === "switchactive") {
            $main->getChart($id)->changeActive();
            header("Location: index.php?admin&page=av");
        }

        if (($_GET["status"] ?? "") === "error") {
            $form->addText("Something wend wrong!");
            echo($form->show());
        }

        $charts = $main->getChart($id);
        $cl = $charts->get();
        if (($action === "add") || count($cl) < 1) {
            $song = $main->getSong()->get();
            $a = [];
            foreach ($song as $s) {
                $a[] = $s["id"];
            }
            $form->addTitle("Neue Abstimmungs Hinzufügen");
            $form->addInput("Song IDs", "Trennung mit ,", "songids", "", true);
            $form->addCalender("Start Datum", "", "startdate");
            $form->addCalender("Ende Datum", "", "enddate");
            $form->addButton("Hinzufügen", "button", "avadd");
            echo($form->show());
        } else
            if (count($cl) === 1) {
                try {
                    foreach ($cl as $key => $value) {
                        $id = $id !== 0 ? $id : $value["id"];
                    }
                } catch (Exception $e) {
                }
                $form->addTitle("Abstimmungs Verwaltung für ID: " . $id);
                $btn = $cl[$id]["active"] ? '<a href="index.php?admin&page=av&id=' . $id . '&action=switchactive" class="btn btn-warning">Deaktivieren</a>' :
                    '<a class="btn btn-success" href="index.php?admin&page=av&id=' . $id . '&action=switchactive">Activieren</a>';
                $html = 'Bei dieser Abstimmung haben zum jetzigen Zeitpunkt <b>' . count($cl[$id]["votes"]) . '</b> User abgestimmt.<br>
                                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                ' . $btn . '<br><br>Jetziger Stand der umfrage <br><br>';
                $form->addText($html);
                echo($form->show());

                try {
                    $form = new Form();
                    $html = "";
                    $topsongs = $oldid === 0 ? ($main->getChart($id)->getTopSongs()) : $charts->getTopSongs();
                    arsort($topsongs);
                    $html .= '<table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Platzierung</th>
                          <th scope="col">Author</th>
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
                  <td>' . Main::addSymbol($songinfos["info"]["author"]) . '</td>
                  <td>' . Main::addSymbol($songinfos["name"]) . '</td>
                  <td>' . $value . '</td>
                  <td><a class="btn btn-primary" href="index.php?song&id=' . $key . '">Anhören</a></td>
                </tr>
                ';
                        $platz++;
                    }
                } catch (Exception $e) {
                }
                $html .= '</tbody></table><br><br><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';

                $form->addText($html);


                echo($form->show());
            } else
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
                    foreach ($cl as $key => $value) {
                        $html .= '
                         <tr>
                          <th scope="row">' . $value["id"] . '</th>
                          <td><a class="btn btn-primary" href="index.php?admin&page=av&id=' . $value["id"] . '">Aufrufen</a></td>
                        </tr>
                        ';
                    }
                    $html .= '</tbody></table><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';
                    $form->addText($html);
                    echo($form->show());
                }

        break;
    }

    case "av-vote":
    {
        if (($_GET["status"] ?? "") === "success") {

            $form = new Form();
            $form->addTitle("Abstimmungs Verwaltung");
            $form->addText("<h1>Vielen Dank für deine Stimme!</h1>");
            echo($form->show());
        }
        $id = $_GET["id"] ?? 0;
        $action = $_GET["action"] ?? "";
        $cl = $main->getChart($id)->get();

        if (count($cl) === 1) {
            try {
                foreach ($cl as $key => $value) {
                    $id = $id !== 0 ? $id : $value["id"];
                }
            } catch (Exception $e) {
            }
            $chart = $main->getChart($id);
            $form = new Form();
            $form->addText('<table id="Table" class="table table-striped" data-toggle="table" data-pagination="true"
           data-search="false">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="Akte">Song Name</th>
            <th scope="col" data-sortable="true" data-field="Akte">Song Author</th>
            <th scope="col" data-sortable="true" data-field="name">Song</th>
            <th scope="col" data-sortable="true" data-field="port">Voting</th>
            <th></th>
        </tr>
        </thead>
        <tbody>');

            foreach ($chart->get()["songid"] as $value) {
                $song = $main->getSong((int)$value);
                $info = $song->get();
                $form->addText('<tr><td>' . Main::addSymbol($info["name"]) . '</td><td>' . Main::addSymbol($info["info"]["author"]) . '</td><td><audio controls><source src="' . $info["file"] . '" ></audio></td>
                                <td><input type="number" min="0" max="3" value="0" name="voting/' . $song->getId() . '"></td></tr>');
            }
            $form->addText("</tbody></table><br><br>");
            $form->addButton("Abstimmen", "button", "avadmin/$id");
            echo($form->show());
        }
        break;
    }

    default:
    {
        $form = new Form();
        $form->addTitle("Admin Site | Abstimmung");
        $form->addText("<a href='index.php?admin&page=av' class='btn btn-primary'>Abstimmung verwalten</a><br><br>");
        $form->addText("<a href='index.php?admin&page=av-vote' class='btn btn-primary'>Punkte hinzufügen</a>");
        echo($form->show());
        $form = new Form();
        $form->addTitle("Admin Site | Songs");
        $form->addText("<a href='index.php?admin&page=addsong' class='btm btn-primary'>Song hinzufügen</a><br><br>
                            <a href='index.php?admin&page=songedit' class='btm btn-primary'>Song ändern</a>");
        echo($form->show());
        $form = new Form();
        $form->addTitle("Admin Site | Neue Songs");
        $form->addText("<a href='index.php?admin&page=newsongs' class='btm btn-primary'>Song aufrufen</a>");
        echo($form->show());
        break;
    }
}