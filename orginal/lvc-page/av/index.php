<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
use wcf\system\WCF;

$user = WCF::getUser()->userID !== 0;
$main = new Main();
$form = new Form();

$id = $_GET['id'] ?? 0;
$chart = $main->getChart($id);

if (count($chart->get()) < 1)
{
    $form->addTitle("Charts");
    $form->addText('Es wurden noch keine Charts erstellt.<br><br><a href="index.php">Zurück</a>');
}
else {

    if ($id === 0) {
        $form->addTitle("Alle Charts");
        $form->addText('<table id="Table" class="table table-striped" data-toggle="table" data-pagination="false"
           data-search="false">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="Akte">ID</th>
            <th scope="col" data-sortable="true" data-field="name">Start Datum</th>
            <th scope="col" data-sortable="true" data-field="port">End Datum</th>
            <th></th>
        </tr>
        </thead>
        <tbody>');

        foreach ($chart->get() as $key => $value){
            if($value["active"]){
                $form->addText("<tr><td>".$value["id"]."</td><td>".date("d.m.Y",strtotime($value["startdate"]))."</td><td>".date("d.m.Y",strtotime($value["enddate"]))."</td>");
                if($user){$form->addText("<td><a href='index.php?av&id=".$value["id"]."'>Abstimmen</a></td>");}
                $form->addText("</tr>");
            }
        }
        $form->addText("</tbody></table>");
    }

    if ($id !== 0) {
        $voted = $chart->hasVoted(WCF::getUser()->userID);
        $status = $_GET["status"] ?? "";
        switch($status){
            case "error":
                $form->addTitle("Fehler");
                $form->addText("<p>Es ist ein Fehler aufgetreten. Bitte versuche es erneut.</p>");
                echo($form->show());
                $form = new Form();
                break;
            case "success":
                $voted=true;
                $form->addTitle("Erfolg");
                $form->addText("<p>Deine Stimmen wurde erfolgreich abgegeben.</p>");
                echo($form->show());
                $form = new Form();
                break;
        }
        $form->addTitle("Charts");
        if(!$chart->get()["active"]) {
            $form->addText('<div class="alert alert-danger" role="alert">Dieser Chart ist nicht aktiv!</div>');
        }else{
            $form->addText('Die Charts werden nach dem ' . date("d.m.Y",strtotime($chart->get()["enddate"])) . ' um ' . date("H:i:s",strtotime($chart->get()["enddate"])) . ' automatisch geschlossen.<br><br>');
        }

        echo($form->show());

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

        foreach ($chart->get()["songid"] as $value){
            $song = $main->getSong((int)$value);
            $info = $song->get();
            $form->addText('<tr><td>'.Main::addSymbol($info["name"]).'</td><td>'.Main::addSymbol($info["info"]["author"]).'</td><td><audio controls><source src="' . $info["file"] . '" ></audio></td>
                                <td><input type="number" min="0" max="3" value="0" name="voting/'.$song->getId().'"></td></tr>');
        }
        $form->addText("</tbody></table><br><br>");
        if(!$voted){
            $form->addButton("Abstimmen", "button", "av/$id");
        }


    }
}
echo($form->show());