<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

use wcf\system\WCF;
$user = WCF::getUser()->userID!==0;
$main = new Main();

$id = $_GET['id'] ?? 0;
$songs = $main->getSong((int)$id);
$form = new Form();
if ($id !== 0) {
    $info = $songs->get();
    if (!empty($info)) {
        $action = $_GET['action'] ?? "";
        if($action==="upvote" || $action==="downvote"){
            $songs->addVote( 1,($action==="downvote"));
            header("Location: index.php?song&id=$id");
        }
        $d = $user ?'<p>Upvotes: <a href="index.php?song&id='.$id.'&action=upvote"><ion-icon name="thumbs-up-outline"></ion-icon>' . $info["upvotes"] . '</a></p><br>
                   <p>Downvotes: <a href="index.php?song&id='.$id.'&action=downvote"><ion-icon name="thumbs-down-outline"></ion-icon>' . $info["downvotes"] . '</a></p><br>' :
                   '<p>Upvotes: ' . $info["upvotes"] . '</p><br>'.'<p>Downvotes: ' . $info["downvotes"] . '</p><br>';
        $form->addTitle("Song: " . $info["name"]);
        $form->addText(
            '<audio controls><source src="' . $info["file"] . '" ></audio><br>
                   <p>Infos:</p><br><br>
                   <ul>
                      <li>Author: ' . $info["info"]["author"] . '</li>
                      <li>Info Text: ' . $info["info"]["infotxt"] . '</li>
                      <li>Upload date: ' . $info["info"]["uploaddate"] . '</li>
                   </ul><br>
                   '.$d);
        echo($form->show());
        foreach ($info["comments"] as $key => $value) {
            $form = new Form();
            $form->addText('<h5>' . $value["name"] . '</h5>
                            <h6>   ' . $value["comment"] . '</h6>
                            <h6>Vom: ' . $value["time"] . '</h6>');
            echo($form->show());

        }
        $form = new Form();
        if($user) {
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
    $pageurl = "index.php?song/";
    $name = $_GET['name'] ?? "";
    if($name!==""){$pageurl .="&name=".$name;}
    $limit = 25;
    $maxsite = (count($songs->getAll()) / 20);
    $page = $_GET["page"] ?? 1;
    $site = 1;

    if(isset($_GET['limit'])){
        $limit = (int)$_GET['limit'] < 1 ? 1 : (int)$_GET['limit'];
        $limit = $limit<=100 ? !($limit<10) ? $limit : 10 : 100;
        $pageurl .= "&limit=".$limit;
    }

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
            <th scope="col" data-sortable="true" data-field="Akte">Name</th>
            <th scope="col" data-sortable="true" data-field="name">Author</th>
            <th scope="col" data-sortable="true" data-field="port">Datum</th>
            <th scope="col" data-field="date"></th>
        </tr>
        </thead>
        <tbody>
            ';

    $a = $songs->getAll($offset, $limit,$name);
    foreach($a as $key => $value){
        if($value["active"]===true) {
            $html .= '<tr>
                         <th scope="row">' . $value["name"] . '</th>
                         <td>' . $value["info"]["author"] . '</td>
                         <td>' . $value["info"]["uploaddate"] . '</td>
                         <td><a href="index.php?song/&id=' . $value["id"] . '">Anhören</a></td>
                    </tr>';
        }
    }

    $html.='   
        </tbody>
    </table>
    ';

    $form->addText($html);
    echo($form->show());
    //$pagesel = new wcf\system\lvc\Pagenation($maxsite, $site, "index.php?song&page=");
    //echo($pagesel->build());
}