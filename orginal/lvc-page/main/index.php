<?php

session_start();


use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$main->init();

use wcf\system\WCF;
$userid = WCF::getUser()->userID;
$user = $userid!==0;

$form = new Form();

$form->addTitle("Herzlich willkommen bei den Low Vision Charts,<br>den Charts von Sehbehinderten für den Rest der Welt.");
$form->addText("Da sich so viele Musikerinnen und Musiker beim International Low Vision Songcontest des
Jugendclubs des DBSV beworben haben, dass sogar welche aussortiert werden mussten, war für uns
schnell klar, da muss was Regelmäßiges her.<br> Damit war an einem schönen Frühlingstag die Low
Vision Charts Idee geboren.<br> Wenn ihr euch bis hier hergefunden habt, seid ihr schon fast ein Teil
dieser Idee.<br> Stimmt jetzt für euren Favoriten ab, macht einen oder eine Künstlerin glücklich, und last
die Idee leben.");

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
    $active = [];
    $deactive = [];
    $i = 0;
    $main->arrsort($charts,"id");
    foreach ($charts as $key => $value){
        if($value["active"] && !$main->getChart($key)->hasVoted($userid)){
            $active[$key] = $value;
        }else{
            $deactive[$key] = $value;
        }
    }
    foreach($active as $key => $value){
        if($i===0){
            $form->addText('<tr><th colspan="4" scope="row">Neue Charts</th></tr>');
            $i=1;
        }
        $form->addText("<tr><td>".$value["id"]."</td><td>".date("d.m.Y",strtotime($value["startdate"]))."</td><td>".date("d.m.Y",strtotime($value["enddate"]))."</td>");
        if($user){$form->addText("<td><a href='index.php?av&id=".$value["id"]."'>Abstimmen</a></td>");}
        $form->addText("</tr>");
        $chartsset=true;
    }
    foreach($deactive as $key => $value){
        if($i<=1){
            $form->addText('<tr><th colspan="4" scope="row">Alte Charts</th></tr>');
            $i=2;
        }
        $form->addText("<tr><td>".$value["id"]."</td><td>".date("d.m.Y",strtotime($value["startdate"]))."</td><td>".date("d.m.Y",strtotime($value["enddate"]))."</td>");
        if($user){$form->addText("<td><a href='index.php?av&id=".$value["id"]."'>Abstimmung ansehen</a></td>");}
        $form->addText("</tr>");
        $chartsset=true;
    }
    $form->addText("</tbody></table>");
}
if(!$chartsset) {
    $form = new Form();
    $form->addTitle("Zurzeit gibt es keine Charts.");
    $form->addText("Wir arbeiten daran, die Charts zu erstellen. Bitte habt einen Moment Geduld.");
}

echo($form->show());

$form = new Form();
$form->addTitle("Sendetermine");
$form->addText('
Jeden 2 Freitag um 20:00 Uhr, ohrfunk.de<br>

Jeden 2. Samstag, 10:00 Uhr, ohrfunk.de<br>

Jeden 2. Sonntag, 15:00 Uhr, MFC Radio<br>

Jeden 2. Mittwoch, 9:00 Uhr, MFC Radio<br>

Jeden 3. Sonntag, 15:00 Uhr, MFC Radio<br>

Jeden 3. Mittwoch, 9:00 Uhr, MFC Radio<br>

Jeden 4. Sonntag, 15:00 Uhr, MFC Radio<br>

Jeden 4. Mittwoch, 9:00 Uhr, MFC Radio<br>');
echo($form->show());
