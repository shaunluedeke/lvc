<?php

session_start();

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$main = new Main();
$main->init();

$dellist = $main->getLog();
$newlist = $main->getLog(true);

$maxsite = (int)((count($main->getLog()) + count($main->getLog(true))) / 20);
$page = $_GET["page"] ?? 1;
$site = 1;
if(is_numeric($page)) {
    $site = ((int)$page > $maxsite ? $page : 20);
    $site = ($site < 1) ? 1 : $site;
}
$offset = 20 * ($site - 1);
$limit = 20 * $site;
$new = $_GET["status"] ?? 2;


$html = '
   
    <table id="Table" class="table table-striped" data-toggle="table" data-pagination="false"
           data-search="false">
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

$page = new wcf\system\lvc\Pagenation($maxsite, $site, "index.php?log&page=");
echo($page->build());