<?php

session_start();


use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$main->init();

use wcf\system\WCF;
$user = WCF::getUser()->userID!==0;

$form = new Form();

$form->addTitle("Herzlich Willkommen auf den Seiten der Low Vision Charts.");
$form->addText("Hier findet ihr jeden Monat die Top10 eurer Lieblingssongs von Sehbehinderten- und blinden Musiker*innen.<br>
Stimmt ab und gestaltet mit. Je mehr Stimmen ein Beitrag bekommt, desto höher steigt er in den Charts, ganz einfach!<br>
Weil allein abstimmen ja vollkommen uncool ist, ist das kopieren des Seitenlinks ausdrücklich erwünscht.");

echo($form->show());

$form = new Form();

//charts abfrage
$charts = $main->getChart()->get();
$chartsset = false;
if(count($charts)> 0) {

    $form->addTitle("Die Top 10 der Letzten Charts");
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

    foreach ($charts as $key => $value){
        if($value["active"]){
            $form->addText("<tr><td>".$value["id"]."</td><td>".date("d.m.Y",strtotime($value["startdate"]))."</td><td>".date("d.m.Y",strtotime($value["enddate"]))."</td>");
            if($user){$form->addText("<td><a href='index.php?av&id=".$value["id"]."'>Abstimmen</a></td>");}
            $form->addText("</tr>");
            $chartsset=true;
        }
    }
    $form->addText("</tbody></table>");
}
if(!$chartsset) {
    $form = new Form();
    $form->addTitle("Zurzeit gibt es keine Charts.");
    $form->addText("Wir arbeiten daran, die Charts zu erstellen. Bitte habt einen Moment Geduld.");
}

echo($form->show());