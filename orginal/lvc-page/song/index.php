<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$form = new Form();
$main = new Main();

$id = ($_GET['id']) ?? 0;
$id = is_int($id) ? (int)($id) : 0;
if ($id !== 0) {
    $info = $main->getSong($id);
    if (!empty($info)) {
        $form->addTitle("Song: " . $info["name"]);
        $form->addText(
            '<audio src="' . $info["file"] . '"></audio><br>
                   <p>Infos:</p><br><br>
                   <ul>
                      <li>Author: ' . $info["info"]["author"] . '</li>
                      <li>Info Text: ' . $info["info"]["infotxt"] . '</li>
                      <li>Upload date: ' . $info["info"]["uploaddate"] . '</li>
                   </ul><br>
                   <p>Upvotes: ' . $info["upvotes"] . '</p><br>
                   <p>Downvotes: ' . $info["downvotes"] . '</p><br>');
        foreach ($info["comments"] as $key => $value) {

            $form->addText('<h5>' . $value["name"] . '</h5>
                            <h6>' . $value["comment"] . '</h6>
                            <h6>Von:' . $value["time"] . '</h6>');
            $form->addInput("New Comment", "", "newcomment", "", true);
            $form->addHidden("songid", $id);
            $form->addButton("Hinzufügen", "button", "addcomment");
        }
    }else{
        $form->addTitle("Song: " . $id);
        $form->addText('<h4>Der Song mit der ID gibt es nicht!</h4>');
    }
    echo($form->show());
}else{
    $maxsite = (count($main->getSong()) / 20);
    $page = $_GET["page"] ?? 1;
    $site = 1;
    if (is_numeric($page)) {
        $site = ((int)$page > $maxsite ? $page : 20);
        $site = ($site < 1) ? 1 : $site;
    }
    $form->addTitle("Song Page: ".$site);
    $offset = (20 * ($site - 1));
    $limit = 20 * ($site);
    $html = '
   
    <table id="Table" class="table table-striped" data-toggle="table" data-pagination="false"
           data-search="false">
        <thead>
        <tr>
            <th scope="col" data-sortable="true" data-field="id">ID</th>
            <th scope="col" data-sortable="true" data-field="Akte">Name</th>
            <th scope="col" data-sortable="true" data-field="name">Author</th>
            <th scope="col" data-sortable="true" data-field="port">Datum</th>
            <th scope="col" data-field="date"></th>
        </tr>
        </thead>
        <tbody>
            ';

    $a = $main->getAllSong($offset, $limit);
    foreach($a as $key => $value){
        $html.='<tr>
                         <th scope="row">'.$value["id"].'</th>
                         <td>'.$value["name"].'</td>
                         <td>'.$value["info"]["author"].'</td>
                         <td>'.$value["info"]["date"].'</td>
                         <td><a href="index.php?song/&id='.$value["id"].'">Anhören</a></td>
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
    
    $form->addText($html);
    echo($form->show());
    $page = new wcf\system\lvc\Pagenation($maxsite, $site, "index.php?top&page=");
    echo($page->build());
}

