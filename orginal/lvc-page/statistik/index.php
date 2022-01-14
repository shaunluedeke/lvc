<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$form = new Form();
$main = new Main();
$song = $main->getSong();
$form->addTitle("Top List");
$form->addText("Das sind alle Songs in einer Top Liste.");
$form->show();
$maxsite = (count($song->getTopSongs()) / 20);
$page = $_GET["page"] ?? 1;
$site = 1;
if (is_numeric($page)) {
    $site = ((int)$page > $maxsite ? $page : 20);
    $site = ($site < 1) ? 1 : $site;
}

$offset = (20 * ($site - 1));
$limit = 20 * ($site);

$form = new Form();

$html = ' 

    <table id="Table" class="table table-striped" data-toggle="table" data-pagination="false"
           data-search="false">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="Akte">Name</th>
            <th scope="col" data-sortable="true" data-field="name">Likes</th>
            <th scope="col" data-sortable="false"></th>
        </tr>
        </thead>
        <tbody>
            ';
$a = $song->getTopSongs($limit, $offset);
foreach ($a as $key => $value) {
    $html .= '
             <tr>
              <th scope="row">' . $value["name"] . '</th>
              <td>' . $value["upvotes"] . '</td>
              <td><a class="btn btn-primary" href="index.php?song&id=' . $value["id"] . '">Anhören</a></td>
            </tr>
            ';
}

$html .= '    
        <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
        </tbody>
    </table>
    ';

$form->addText($html);

echo($form->show());

$page = new wcf\system\lvc\Pagenation($maxsite, $site, "index.php?top&page=");
echo($page->build());