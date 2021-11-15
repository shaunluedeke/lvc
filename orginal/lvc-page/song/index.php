<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;

$form = new Form();
$main = new Main();

$id = ($_GET['id']) ?? 0;
$id = is_int($id) ? (int)($id) : 0;
if($id!==0) {
    $info = $main->getSong($id);
    if(!empty($info)) {
        $form->addTitle("Song: " . $info["name"]);
        $form->addText(
            '<audio src="'.$info["file"].'"></audio>
                   <p>Infos:</p>
                   <ul>
                      <li>Author: '.$info["info"]["author"].'</li>
                      <li>Info Text: '.$info["info"]["infotxt"].'</li>
                      <li>Upload date: '.$info["info"]["uploaddate"].'</li>
                   </ul>
                   <p>Upvotes: '.$info["upvotes"].'</p>
                   <p>Downvotes: '.$info["downvotes"].'</p>');
        foreach($info["comments"] as $key => $value)
        $form->addText('<h5>'.$value["name"].'</h5>
                            <h6>'.$value["comment"].'</h6>
                            <h6>Von:'.$value["time"].'</h6>');
        $form->addInput("New Comment", "", "newcomment","",true);
        $form->addHidden("songid",$id);
        $form->addButton("Hinzufügen","button","addcomment");
    }else{

    }
}else{
    
}