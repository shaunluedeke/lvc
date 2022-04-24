<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
use wcf\system\WCF;

$userid = WCF::getUser()->userID;
$user = $userid !== 0;
$main = new Main();
$form = new Form();

$id = $_GET['id'] ?? 0;
$chart = $main->getChart($id);
if (count($chart->get()) < 1) {
    $form->addTitle("Charts");
    $form->addText('Es wurden noch keine Charts erstellt.<br><br><a href="index.php">Zurück</a>');
} else {
    if ($id === 0) {
        $form->addTitle("Alle Charts");
        $form->addTableHeader(["ID","Start Datum","End Datum",""]);

        $active = [];
        $deactive = [];
        $i = 0;
        foreach ($chart->get() as $key => $value) {
            if ($value["active"] && !$main->getChart($key)->isEnded() && $main->getChart($key)->isStarted() && !$main->getChart($key)->hasVoted($userid)) {
                $active[$key] = $value;
            } else if ($main->getChart($key)->isStarted() || $main->getChart($key)->isEnded()) {
                $deactive[$key] = $value;
            }
        }
        foreach ($active as $key => $value) {
            if ($i === 0) {
                $form->addText('<tr><th colspan="4" scope="row">Neue Charts</th></tr>');
                $i = 1;
            }
            $form->addText("<tr><td>" . $value["id"] . "</td><td>" . date("d.m.Y", strtotime($value["startdate"])) . "</td><td>" . date("d.m.Y", strtotime($value["enddate"])) . "</td>");
            if ($user) {
                $form->addText("<td><a href='index.php?av&id=" . $value["id"] . "'>Abstimmen</a></td>");
            }
            $form->addText("</tr>");
            $chartsset = true;
        }
        foreach ($deactive as $key => $value) {
            if ($i <= 1) {
                $form->addText('<tr><th colspan="4" scope="row">Alte Charts</th></tr>');
                $i = 2;
            }
            $form->addText("<tr><td>" . $value["id"] . "</td><td>" . date("d.m.Y", strtotime($value["startdate"])) . "</td><td>" . date("d.m.Y", strtotime($value["enddate"])) . "</td>");
            if ($user) {
                $form->addText("<td><a href='index.php?av&id=" . $value["id"] . "'>Abstimmung ansehen</a></td>");
            }
            $form->addText("</tr>");
            $chartsset = true;
        }
        $form->addTableFooter();
    }

    if ($id !== 0) {
        $voted = $chart->hasVoted($userid);
        $status = $_GET["status"] ?? "";
        switch ($status) {
            case "error":
                $form->addTitle("Fehler");
                $error = (int)($_GET["error"] ?? 103);
                switch ($error) {
                    case 101:
                        $form->addText("<p>Es ist ein Fehler aufgetreten. Sie haben zwei oder mehreren Songs die gleiche Platzierung gegeben.</p>");
                        break;
                    case 102:
                        $form->addText("<p>Es ist ein Fehler aufgetreten. Sie müssen für drei Songs abstimmen.</p>");
                        break;
                    default:
                        $form->addText("<p>Es ist ein Fehler aufgetreten. Bitte versuche sie es später erneut.</p>");
                        break;
                }
                echo($form->show());
                $form = new Form();
                break;
            case "success":
                $voted = true;
                $form->addTitle("Erfolg");
                $form->addText("<p>Deine Stimmen wurde erfolgreich abgegeben.</p>");
                echo($form->show());
                $form = new Form();
                break;
        }

        $form->addTitle("Charts");
        $active = ($chart->get()[$id]["active"] && $chart->isStarted() && !$chart->isEnded());
        if (!$active) {
            if ($chart->isEnded()) {
                $form->addText('Die Charts wurden am ' . date("d.m.Y", strtotime($chart->get()[$id]["enddate"])) . ' um ' . date("H:i:s", strtotime($chart->get()[$id]["enddate"])) . ' automatisch geschlossen.<br><br>');
            } else if (!$chart->isStarted()) {
                $form->addText('Die Charts werden am ' . date("d.m.Y", strtotime($chart->get()[$id]["startdate"])) . ' um ' . date("H:i:s", strtotime($chart->get()[$id]["startdate"])) . ' automatisch geöffnet.<br><br>');
            } else {
                $form->addText('Die Charts wurden manuel geschlossen.<br><br>');
            }
        } else {
            $form->addText('Die Charts werden nach dem ' . date("d.m.Y", strtotime($chart->get()[$id]["enddate"])) . ' um ' . date("H:i:s", strtotime($chart->get()[$id]["enddate"])) . ' automatisch geschlossen.<br><br>');
        }

        echo($form->show());
        $form = new Form();

        if ($active && !$voted) {
            $form->addTableHeader(["","Song Name", "Song Author","Song","Voting"]);
            $chartssongs = $chart->get();
            foreach ($chartssongs[$id]["songid"] as $value) {
                $song = $main->getSong((int)$value);
                $info = $song->get();
                $new = $chart->isNewSong($value);
                $form->addText('<tr>
                                        <td style="width: 5%">' . ($new ? '<span class="badge badge-success">Neu</span>' : '') . '</td>
                                        <td style="width: 30%">' . Main::addSymbol($info["name"]) . '</td>
                                        <td style="width: 35%">' . Main::addSymbol($info["info"]["author"]) . '</td>
                                        <td style="width: 15%"><a href="index.php?song&id='.$value.'&autoplay=1" target="_blank">Anhören</a></td>
                                        <td style="width: 15%"><input style="width: 100%" type="number" min="0" max="3" placeholder="Platz 1 bis 3" name="voting/' . $song->getId() . '"></td>
                                     </tr>');
            }
            $form->addText("</tbody></table><br><br>");
            $form->addButton("Abstimmen", "button", "av/$id");
        } else if ($active && $voted) {
            $form->addText('<table id="Table" class="table table-striped" data-toggle="table" data-pagination="true"
           data-search="false">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="Akte">Platzierung</th>
            <th></th>
            <th scope="col" data-sortable="true" data-field="name">Song Name</th>
            <th scope="col" data-sortable="true" data-field="author">Song Author</th>
            <th scope="col" data-sortable="true" data-field="name">Song</th>
        </tr>
        </thead>
        <tbody>');
            $place = 1;
            $top = $chart->getTopSongs();
            arsort($top, SORT_NUMERIC);
            $topsongs = $chart->getVotesfromUser($userid);
            arsort($topsongs, SORT_NUMERIC);
            $s = [];
            foreach ($top as $key => $value) {
                $s[$key] = $place;
                $place++;
            }
            foreach ($topsongs as $key => $value) {
                if ($value === 0) {
                    continue;
                }
                $song = $main->getSong((int)$key);
                $info = $song->get();
                $new = $chart->isNewSong((int)$key);
                $form->addText('<tr>
                                        <td style="width: 5%">' . $s[$key] . '</td>
                                        <td style="width: 5%">' . ($new ? '<span class="badge badge-success">Neu</span>' : '') . '</td>
                                        <td style="width: 20%">' . Main::addSymbol($info["name"]) . '</td>
                                        <td style="width: 25%">' . Main::addSymbol($info["info"]["author"]) . '</td>
                                        <td style="width: 35%"><a href="index.php?song&id='.$value.'&autoplay=1" target="_blank">Anhören</a></td>
                                     </tr>');
            }
            $form->addText("</tbody></table><br><br>");
        } else {
            $form->addText('<table id="Table" class="table table-striped" data-toggle="table" data-pagination="true"
           data-search="false">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="Akte">Platzierung</th>
            <th></th>
            <th scope="col" data-sortable="true" data-field="name">Song Name</th>
            <th scope="col" data-sortable="true" data-field="author">Song Author</th>
            <th scope="col" data-sortable="true" data-field="name">Song</th>
        </tr>
        </thead>
        <tbody>');

            $place = 1;
            $topsongs = $chart->getTopSongs();
            arsort($topsongs);
            foreach ($topsongs as $key => $value) {
                $song = $main->getSong((int)$key);
                $info = $song->get();
                $new = $chart->isNewSong($key);
                $form->addText('<tr>
                                        <th scope="row">Platz ' . $place . '</th>
                                        <td>' . ($new ? '<span class="badge badge-success">Neu</span>' : '') . '</td>
                                        <td>' . Main::addSymbol($info["name"]) . '</td>
                                        <td>' . Main::addSymbol($info["info"]["author"]) . '</td>
                                        <td><a href="index.php?song&id='.$value.'&autoplay=1" target="_blank">Anhören</a></td>
                                    </tr>');
                $place++;
            }
            $form->addText("</tbody></table><br><br>");
        }

    }
}
echo($form->show());