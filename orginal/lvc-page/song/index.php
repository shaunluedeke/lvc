<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

use wcf\system\WCF;
$userid = WCF::getUser()->userID;
$user = $userid!==0;
$main = new Main();

$id = $_GET['id'] ?? 0;
$songs = $main->getSong((int)$id);
$form = new Form();
if ($id !== 0) {
    $info = $songs->get();
    if (!empty($info)) {
        if(!$info["active"]) {
            $form->addTitle("Song is not active");
            $form->addText('Please contact the admin to activate this song or choose another one.<br><br><a href="index.php?song">Back</a>');
        }else {
            $autoplay = (int)($_GET['autoplay'] ?? 0);
            $action = $_GET['action'] ?? "";
            if ($action === "upvote" || $action === "downvote") {
                $songs->addVote($userid,1, ($action === "downvote"));
                header("Location: index.php?song&id=$id");
            }
            $d = $user ? '<p>Upvotes: <a href="index.php?song&id=' . $id . '&action=upvote" ><ion-icon name="thumbs-up-sharp" '.($songs->hasVoted($userid,false) ? 'style="color: #03e3fc" alt="Bereits Geliket"' : 'alt="Noch nicht Geliket"').'></ion-icon>' . count($info["upvotes"] ?? array()) . '</a></p><br>
                   <p>Downvotes: <a href="index.php?song&id=' . $id . '&action=downvote"><ion-icon name="thumbs-down-sharp" '.($songs->hasVoted($userid,true) ? 'style="color: #03e3fc" alt="Bereits Disliked"' : 'alt="Noch nicht Disliked"').'></ion-icon>' . count($info["downvotes"]?? array()) . '</a></p><br>' :
                '<p>Upvotes: ' . $info["upvotes"] . '</p><br>' . '<p>Downvotes: ' . $info["downvotes"] . '</p><br>';
            $form->addTitle("Song: " . Main::addSymbol($info["name"]));
            $infotext = $info["info"]["infotxt"] === "" ? "" : '<li>Info Text: ' . Main::addSymbol($info["info"]["infotxt"]) . '</li>';
            $audio = $autoplay === 0 ? '<audio controls><source src="' . $info["file"] . '" ></audio>' : '<audio controls autoplay><source src="' . $info["file"] . '" ></audio>';
            $form->addText($audio.
                '<br>
                   <p>Infos:</p><br><br>
                   <ul>
                      <li>Author: ' . Main::addSymbol($info["info"]["author"]) . '</li>
                      ' . $infotext . '
                      <li>Upload date: ' . $info["info"]["uploaddate"] . '</li>
                   </ul><br>
                   ' . $d);
            echo($form->show());
            foreach ($info["comments"] as $key => $value) {
                $form = new Form();
                $form->addText('<h5>' . Main::addSymbol($value["name"]) . '</h5>
                            <h6>   ' . Main::addSymbol($value["comment"]) . '</h6>
                            <h6>Vom: ' . $value["time"] . '</h6>');
                echo($form->show());

            }
            $form = new Form();
            if ($user) {
                $form->addInput("New Comment", "", "newcomment", "", true);
                $form->addHidden("songid", $id);
                $form->addButton("Hinzuf??gen", "button", "addcomment");
            }
        }
    }else{
        $form->addTitle("Song: " . $id);
        $form->addText('<h4>Der Song mit der ID gibt es nicht!</h4>');
    }

}
else{
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
                         <th scope="row">' . Main::addSymbol($value["name"]) . '</th>
                         <td>' . Main::addSymbol($value["info"]["author"]) . '</td>
                         <td>' . $value["info"]["uploaddate"] . '</td>
                         <td><a href="index.php?song/&id=' . $value["id"] . '">Anh??ren</a></td>
                    </tr>';
        }
    }

    $html.='   
        </tbody>
    </table>
    ';

    $form->addText($html);
}
echo($form->show());