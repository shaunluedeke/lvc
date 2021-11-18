<?php

session_start();

use wcf\system\WCF;
use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$main->init();

$dellist = $main->getLog();
$newlist = $main->getLog(true);

$maxsite = ((count($main->getLog()) + count($main->getLog(true))) / 20);
$page = ($_GET["page"] ?? 1);
$site = ($page < 1) ? 1 : ($page > $maxsite ? $page : 20);

$offset = (20 * ($site - 1));
$limit = 20 * ($site);
$new = $_GET["status"] ?? 2;


$html = '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css">
    
    <table id="Table" class="table table-striped table-dark" style="color:white;" data-toggle="table" data-pagination="true"
           data-search="true">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="id">ID</th>
            <th scope="col" data-sortable="true" data-field="Akte">Name</th>
            <th scope="col" data-sortable="true" data-field="name">Author</th>
            <th scope="col" data-sortable="true" data-field="port">Datum</th>
            <th scope="col" data-sortable="true" data-field="date">Status</th>
        </tr>
        </thead>
        <tbody>
            ';

        $a = $main->getAllLog($offset, $limit,$new);
        foreach($a as $key => $value){
            $html.='<tr>
                         <th scope="row">'.$value["id"].'</th>
                         <td>'.$value["name"].'</td>
                         <td>'.$value["info"]["author"].'</td>
                         <td>'.$value["date"].'</td>
                         <td>'.($value["status"] ? "Neu" : "Gelöscht").'</td>
                    </tr>';
        }

$html.='    
        <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
        </tbody>
    </table>
    ';


$form = new Form();
$form->addTitle("Logs");
$form->addText($html);
echo($form->show());

$page = new Pagenation($maxsite, $site, "index.php?log&page=");
echo($page->build());