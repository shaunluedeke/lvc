<?php

use wcf\system\lvc\Main;
use wcf\system\lvc\Form;
use wcf\system\lvc\Pagenation;

$form = new Form();
$main = new Main();

$form->addText("Das sind alle Songs in einer Top Liste.");
$form->show();
$maxsite = (count($main->getTopSongs())/20);
$site = (($_GET["page"] ?? 1) < 1) ? 1 : (($_GET["page"] > $maxsite) ? $_GET["page"] : 20);

$offset = (20 * ($site-1));
$limit = 20*($site);

$form = new Form();
$html = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

 <table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Name</th>
      <th scope="col">Likes</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
';
foreach($main->getTopSongs($limit,$offset) as $key=>$value){
    $html .= '
     <tr>
      <th scope="row">'.$value["id"].'</th>
      <td>'.$value["name"].'</td>
      <td>'.$value["upvotes"].'</td>
      <td><a class="btn btn-primary" href="index.php?song&id='.$value["id"].'">Anhören</a></td>
    </tr>
    ';
}
$html.='</tbody></table>';

echo($form->show());

$page = new Pagenation($maxsite, $site, "index.php?top&page=");
echo($page->build());