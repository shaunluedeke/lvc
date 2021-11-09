<?php
class Form {

    private $vars = array();
    private $lastTitle = "";
    private $lastnum = 0;
    private $url = "";
    private $style = "";
    private $method = "post";

    function __construct() {

        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this,$f='__construct'.$i)) {

            call_user_func_array(array($this,$f),$a);
        }
    }


    function __construct1($api) {

    }

    function __construct2($api, $url) {

        $this->url = $url;
    }

    function __construct3($api, $url, $style) {

        $this->url = $url;
        $this->style = $style;
    }

    public function addTitle($title) {

        $this->lastTitle = $title;
        $this->lastnum = 0;
    }
    public function setMethod($method) {

        $this->method = $method;
    }

    public function setlastElementStyle($style) {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["style"] = $style;
    }

    public function setlastElementExtra($style) {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["extra"] = $style;
    }

    public function addTextarea($title, $text, $id, $value = "") {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "textarea";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->lastnum ++;
    }



    public function addSelect($title, $text, $id, ...$args) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "select";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $i = 0;
        foreach ($args as &$arg) {

            $this->vars[$this->lastTitle][$this->lastnum]["option"][$i] = $arg;
            $i++;
        }

        $this->lastnum ++;
    }

    public function setSelect($text) {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["select"] = $text;
    }

    public function addList($title, $text, $id, ...$args) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "list";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $i = 0;
        foreach ($args as &$arg) {

            $this->vars[$this->lastTitle][$this->lastnum]["option"][$i] = $arg;
            $i++;
        }

        $this->lastnum ++;
    }

    public function addInput($title, $text, $id, $value = "") {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "input";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }

    public function addUpload($title, $text, $id) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "upload";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }

    public function addNumber($title, $text, $id, $value = 0, $step = 1, $min = 0, $max = 10000) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "number";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["step"] = $step;
        $this->vars[$this->lastTitle][$this->lastnum]["min"] = $min;
        $this->vars[$this->lastTitle][$this->lastnum]["max"] = $max;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }


    public function addRage($title, $text, $id, $value = 0, $step = 1, $min = 0, $max = 10000) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "rage";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["step"] = $step;
        $this->vars[$this->lastTitle][$this->lastnum]["min"] = $min;
        $this->vars[$this->lastTitle][$this->lastnum]["max"] = $max;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }

    public function addHidden($id, $value = "") {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "hidden";
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }



    public function addCheck($title, $text, $value1, $value2, $id) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "check";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value1"] = $value1;
        $this->vars[$this->lastTitle][$this->lastnum]["value2"] = $value2;
        $this->vars[$this->lastTitle][$this->lastnum]["check"] = false;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }
    public function setCheck($checked = false){
        $this->vars[$this->lastTitle][$this->lastnum - 1]["check"] = $checked;
    }


    public function addButton($value1, $id, $value2) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "button";
        $this->vars[$this->lastTitle][$this->lastnum]["value1"] = $value1;
        $this->vars[$this->lastTitle][$this->lastnum]["value2"] = $value2;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }



    public function addText($text) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "text";
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = "";
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->lastnum ++;
    }



    public function addCalender($id) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "calender";
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }



    public function addTextTemplate($toid, $text, $name, $id) {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "texttemplate";
        $this->vars[$this->lastTitle][$this->lastnum]["name"] = $name;
        $this->vars[$this->lastTitle][$this->lastnum]["toid"] = $toid;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum ++;
    }



    public function show() {

        echo '<form style="'.$this->style.'" method="'.$this->method.'" enctype="multipart/form-data" action="'.$this->url.'/">';
        foreach ($this->vars as $key => $value) {

            echo '<section class="section">';
            if($key != "") {
                echo '<h2 class="sectionTitle">'.$key.'</h2>';
            }
            foreach ($value as &$var) {

                $id = $var["id"];
                $style = "";
                $extra = "";
                if(isset($var["style"])) {
                    $style = ' style="'.$var["style"].'" ';
                }
                if(isset($var["extra"])) {
                    $extra = ' '.$var["extra"].' ';
                }


                if($var["art"] == "button") {

                    echo '<button '.$style.' '.$extra.' onclick="this.style.visibility = \'hidden\';" class="buttonPrimary" accesskey="s" id="'.$id.'" value="'.$var["value2"].'" name="'.$id.'" data-type="save">'.$var["value1"].'</button>';
                    continue;
                } else if($var["art"] == "calender") {

                    echo '<input '.$style.' '.$extra.' id="'.$id.'" class="calendersty" type="datetime-local" value="2018-11-01T10:00"/>';
                    continue;

                } else if($var["art"] == "texttemplate") {

                    echo '<input '.$style.' type="button" name="'.$id.'" value="'.$var["name"].'" onClick="setText(\''.$var["toid"].'\', \''.$var["text"].'\');'.$extra.'">';
                    continue;
                } else if($var["art"] == "text") {

                    echo $var["text"];
                    continue;
                } else if($var["art"] == "hidden") {

                    echo '<input '.$style.' '.$extra.' type="hidden" id="'.$id.'" name="'.$id.'" value="'.$var["value"].'">';
                    continue;
                }



                echo '<dl class="'.$id.'Input">';
                echo '<dt><label for="'.$id.'">'.$var["title"].'</label></dt>';
                echo '<dd>';


                if($var["art"] == "input") {

                    echo '<input '.$style.' '.$extra.' type="text" id="'.$id.'" name="'.$id.'" value="'.$var["value"].'" class="medium">';
                } else if($var["art"] == "upload") {
                    echo '<input '.$style.' '.$extra.' style="border: 2px dashed #e0e4e8;padding: 30px;padding-left: 200px;padding-right: 200px;" type="file" name="'.$id.'"  id="'.$id.'" accept="image/png">';


                } else if($var["art"] == "number") {

                    echo '<input '.$style.' '.$extra.' type="number" id="'.$id.'" name="'.$id.'" min="'.$var["min"].'" max="'.$var["max"].'" step="'.$var["step"].'" value="'.$var["value"].'" class="medium">';
                } else if($var["art"] == "rage") {

                    echo '<input '.$style.' '.$extra.' type="range" id="'.$id.'" name="'.$id.'" min="'.$var["min"].'" max="'.$var["max"].'" step="'.$var["step"].'" value="'.$var["value"].'" class="medium">';

                } else if($var["art"] == "list") {

                    echo '<input '.$style.' '.$extra.' type="text" id="'.$id.'" name="'.$id.'" list="list_'.$id.'" class="medium">';
                    echo '<datalist id="list_'.$id.'">';

                    foreach ($var["option"] as &$key) {

                        echo '<option value="'.$key.'">';
                    }

                    echo '</datalist>';

                } else if($var["art"] == "textarea") {

                    echo '<textarea '.$style.' '.$extra.' id="'.$id.'" name="'.$id.'" cols="40" rows="5" >'.$var["value"].'</textarea>';


                } else if($var["art"] == "select") {

                    echo '<select '.$style.' '.$extra.' id="'.$id.'" name="'.$id.'">';
                    $true = true;
                    foreach (($var["option"]) as $key) {

                        if(isset($var["select"])) {
                            if($var["select"] == $key) {
                                echo '<option value="'.$key.'" selected="">'.$key.'</option>';
                            } else {
                                echo '<option value="'.$key.'">'.$key.'</option>';
                            }
                        } else {
                            if($true) {

                                $true = false;
                                echo '<option value="'.$key.'" selected="">'.$key.'</option>';
                            } else {

                                echo '<option value="'.$key.'">'.$key.'</option>';
                            }
                        }


                    }

                    echo '</select>';
                } else if($var["art"] == "check") {
                    if($var["check"]){
                        echo '<ol '.$style.' '.$extra.' class="flexibleButtonGroup optionTypeBoolean">';
                        echo '<li>';
                        echo '  <input type="radio" id="check'.$id.'" checked="" name="'.$id.'" value="'.$var["value1"].'">';
                        echo '  <label for="check'.$id.'" class="green">';
                        echo '  <span class="icon icon16 fa-check"></span> ';
                        echo '  '.$var["value1"].'</label>';
                        echo '</li>';
                        echo '<li>';
                        echo '  <input type="radio" id="checkno'.$id.'" name="'.$id.'" value="'.$var["value2"].'">';
                        echo '  <label for="checkno'.$id.'" class="red">';
                        echo '  <span class="icon icon16 fa-times"></span> ';
                        echo    $var["value2"].'</label>';
                        echo '</li>';
                        echo '</ol>';
                    }else {
                        echo '<ol ' . $style . ' ' . $extra . ' class="flexibleButtonGroup optionTypeBoolean">';
                        echo '<li>';
                        echo '  <input type="radio" id="check' . $id . '" name="' . $id . '" value="' . $var["value1"] . '">';
                        echo '  <label for="check' . $id . '" class="green">';
                        echo '  <span class="icon icon16 fa-check"></span> ';
                        echo '  ' . $var["value1"] . '</label>';
                        echo '</li>';
                        echo '<li>';
                        echo '  <input type="radio" id="checkno' . $id . '" checked="" name="' . $id . '" value="' . $var["value2"] . '">';
                        echo '  <label for="checkno' . $id . '" class="red">';
                        echo '  <span class="icon icon16 fa-times"></span> ';
                        echo $var["value2"] . '</label>';
                        echo '</li>';
                        echo '</ol>';
                    }
                }





                echo '<small>'.$var["text"].'</small>';
                echo '</dd>';
                echo '</dl>';
            }

            echo '</section>';
        }

        echo '</form>';
        echo '<script language="JavaScript" type="text/javascript">';
        echo 'function setText(id, text) {';
        echo 'var res = text.replace(new RegExp("<br>", \'g\'), "\n");';
        echo 'document.getElementById(id).value = res;';
        echo '}';
        echo '</script>';

    }



}
?>