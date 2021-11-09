<?php
class Pagenation {

private $page_array = array();
private $maxpage;
private $aktivpage;
private $url;

function __construct($api, $maxpage, $aktivpage, $url) {
$this->maxpage = $maxpage;
$this->aktivpage = $aktivpage;
$this->url = $url;
}


function build() {
if($this->maxpage <2){
return '';
}
$html = '<div class="messageListPagination" style="padding: 30px 0;">';
    $html .= '<nav class="pagination">';
        $html .= '<ul>';

            if($this->aktivpage <= 1) {
            $html .= '<li class="skip disabled"><span href="'.$this->url.($this->aktivpage-1).'" class="icon icon24 fa-chevron-left" data-tooltip="Vorherige Seite"></span></li>';

            } else {
            $html .= '<li class="skip"><a href="'.$this->url.($this->aktivpage-1).'" class="icon icon24 fa-chevron-left" data-tooltip="Vorherige Seite"></a></li>';

            }

            $sextra = 0;
            $eextra = $this->maxpage;
            if($this->aktivpage <= 4) {
            $sextra = 4;
            } else if($this->maxpage - $this->aktivpage <= 3) {
            $eextra = $this->maxpage - 3;
            }
            $lasti = 0;
            for($i=1;$i<=$this->maxpage;$i++) {
            if($i == 1) {}
            else if($i == $this->maxpage) {}
            else if($i >= $this->aktivpage - 1 && $this->aktivpage + 1 >= $i) {}
            else if($i <= $sextra) {}
            else if($i >= $eextra) {}
            else {
            continue;
            }
            if($lasti != $i - 1) {
            $html .= '<li class="jumpTo"><a class="" data-tooltip="Gehe zu Seite">…</a></li>';
            }
            $lasti = $i;
            if($i == $this->aktivpage) {
            $html .= '<li class="active"><span>'.$i.'</span></li>';
            continue;
            }
            $html .= '<li><a href="'.$this->url.$i.'" title="Seite '.$i.'">'.$i.'</a></li>';
            }

            if($this->aktivpage >= $this->maxpage) {
            $html .= '<li class="skip disabled"><span href="'.$this->url.($this->aktivpage-1).'" class="icon icon24 fa-chevron-right" data-tooltip="Vorherige Seite"></span></li>';

            } else {
            $html .= '<li class="skip"><a href="'.$this->url.($this->aktivpage-1).'" class="icon icon24 fa-chevron-right" data-tooltip="Vorherige Seite"></a></li>';

            }

            $html .= '</ul>';
        $html .= '</nav>';
    $html .= '</div>';

return $html;
}

}