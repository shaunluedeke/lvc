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

    case "bcd":
    {
        $bcd = $main->getBrodcastdate();
        $bcdget = $bcd->get($id);
        $action = $_GET['action'] ?? "";
        if ($action === "delete") {
            $bcd->removeDate($id);
            header("Location: index.php?admin&page=bcd");
        }
        if ($action === "add" || count($bcdget) < 1) {
            $form->addTitle("Add Broadcast Date");
            $form->addInput("Name", "Gebe den Name des Senders ein", "name", "", true);
            $form->addURL("Link", "Gebe den Link des Senders ein", "link", "", true);
            $form->addSelect("Wochentag", "", "weekday", ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"], true);
            $form->addNumber("Woche im Monat", "", "delay", 0, 1, 1, 5, true);
            $form->addInput("Gebe die Uhrzeit an (15:00)", "", "time", "", true);
            $form->addButton("Absenden", "button", "bcdadd");
            echo($form->show());
            break;
        }
        $form->addTitle("Broadcast Date");
        $form->addTableHeader(["Name", "Link", "Wochentag", "Woche im Monat","Uhrzeit", ""]);
        foreach ($bcdget as $key => $value) {
            $form->addTableRow([$value["Name"], "<a href='" . $value["Link"] . "' target='_blank'>" . $value["Link"] . "</a>", $bcd->getDayofInt($value["Weekday"]), $value["Delay"], $value["Time"], "<a href='index.php?admin&page=bcd&id=" . $key . "&action=delete'>Delete</a>"],true);
        }
        $form->addTableFooter();

        $form->addText("<a href='index.php?admin&page=bcd&action=add'>Add Broadcast Date</a>");
        echo($form->show());

        break;
    }

    case "api":
    {
        $ip = $_GET['ip'] ?? "";
        $api = $main->getAPI($ip);
        $apiget = $api->get();
        $action = $_GET['action'] ?? "";
        if ($action === "delete") {
            $api->remove();
            header("Location: index.php?admin&page=api");
        }
        if ($action === "add" || count($apiget) < 1) {
            $form->addTitle("Add API");
            $form->addInput("IP", "", "ip", "", true);
            $form->addNumber("Permission", "", "permission", 0, 1, 0, 10, true);
            $form->addButton("Absenden", "button", "apiadd");
            echo($form->show());
            break;
        }
        if ($ip === "") {
            $form->addTitle("API List");
            $form->addTableHeader(["ID", "IP", "Permission", "Active", ""]);
            foreach ($apiget as $key => $value) {
                $form->addTableRow([$key, $value["IP"], $value["Permission"], $value["Active"] ? "Ja" : "Nein", '<a href="index.php?admin&page=api&ip=' . $value["IP"] . '">Aufrufen</a>'], true);
            }
            $form->addTableFooter();
            $form->addText("<br><br><a href='index.php?admin&page=api&action=add'>Add</a><br><br><a href='index.php?admin'>Zurück</a>");
            echo($form->show());
            break;
        } else {
            $form->addTitle("API: " . $ip);
            $form->addInput("IP", "", "ip", $ip, true, true);
            $form->addNumber("Permission", "", "permission", $apiget["Permission"], 1, 0, 10, true);
            $form->addNumber("Active", "", "active", (int)$apiget["Active"], 1, 0, 1, true);
            $form->addButton("Absenden", "button", "apiupdate");
            $form->addText("<br><br><a href='index.php?admin&page=api&action=delete&ip=$ip'>Remove</a><br><br><a href='index.php?admin&page=api'>Zurück</a>");
            echo($form->show());
            break;
        }

        break;
    }

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
                    $form->addTableHeader(["Platzierung","Author","Name","Votes",""]);
                    $platz = 1;

                    foreach ($topsongs as $key => $value) {
                        $songinfos = $main->getSong($key)->get();
                        $form->addTableRow([$platz,Main::addSymbol($songinfos["info"]["author"]),Main::addSymbol($songinfos["name"]),$value,'<a class="btn btn-primary" href="index.php?song&id=' . $key . '">Anhören</a>'],true);
                        $platz++;
                    }
                    $form->addTableFooter();
                } catch (Exception $e) {
                }
                $html .= '</tbody></table><br><br><a class="btn-primary" href="index.php?admin&page=av&action=add">Neue Hinzufügen</a>';

                $form->addText($html);


                echo($form->show());
            } else
                if (count($cl) > 1) {
                    $form->addTitle("Abstimmungs Verwaltung");
                    $form->addTableHeader(["ID",""]);
                    foreach ($cl as $key => $value) {
                        $form->addTableRow([$value["id"],'<a class="btn btn-primary" href="index.php?admin&page=av&id=' . $value["id"] . '">Aufrufen</a>'],true);
                    }
                    $form->addTableFooter();
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
            $form->addTitle("Abstimmungen");
            $form->addTableHeader(["Song Name","Song Author","Song","Voting"]);

            foreach ($chart->get()[$id]["songid"] as $value) {
                $song = $main->getSong((int)$value);
                $info = $song->get();
                $form->addTableRow([Main::addSymbol($info["name"]),Main::addSymbol($info["info"]["author"]),'<audio controls><source src="' . $info["file"] . '" ></audio>','<input type="number" min="0" max="3" value="0" name="voting/' . $song->getId() . '">']);
            }
            $form->addTableFooter();
            $form->addButton("Abstimmen", "button", "avadmin/$id");
        }
        else{
            $form = new Form();
            $form->addTitle("Abstimmungen");
            $form->addTableHeader(["ID","Active",""]);
            foreach ($cl as $key => $value){
                $active = $value["active"] && (int)$value["autoset"] === 1;
                $form->addTableRow([$value["id"],$active?"Aktive":"Deaktiveiert",'<a href="index.php?admin&page=av-vote&id='.$value["id"].'">Abstimmen</a>'],$active);
            }
            $form->addTableFooter();
        }
        echo($form->show());
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
        $form = new Form();
        $form->addTitle("Admin Site | API");
        $form->addText("<a href='index.php?admin&page=api' class='btm btn-primary'>API aufrufen</a>");
        echo($form->show());
        $form = new Form();
        $form->addTitle("Admin Site | Brodcast Dates");
        $form->addText("<a href='index.php?admin&page=bcd' class='btm btn-primary'>Brodcast Dates aufrufen</a>");
        echo($form->show());
        break;
    }
}
?>