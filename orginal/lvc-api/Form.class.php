<?php
namespace wcf\system\lvc;
class Form
{

    private array $vars = array();
    private string $lastTitle = "";
    private int $lastnum = 0;
    private string $url = "index.php?forwarding";
    private string $style = "";
    private string $method = "post";

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setStyle(string $style): void
    {
        $this->style = $style;
    }

    public function addTitle($title): void
    {

        $this->lastTitle = $title;
        $this->lastnum = 0;
    }

    public function setMethod($method): void
    {

        $this->method = $method;
    }

    public function setlastElementStyle($style): void
    {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["style"] = $style;
    }

    public function setlastElementExtra($style): void
    {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["extra"] = $style;
    }

    public function addTextarea($title, $text, $id, $value = "",bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "textarea";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }


    public function addSelect($title, $text, $id, $args, bool $require = false,bool $multi = false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "select";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->vars[$this->lastTitle][$this->lastnum]["multi"] = $multi;
        $i = 0;
        foreach ($args as $arg) {
            $this->vars[$this->lastTitle][$this->lastnum]["option"][$i] = $arg;
            $i++;
        }

        $this->lastnum++;
    }

    public function setSelect($text): void
    {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["select"] = $text;
    }

    public function addList($title, $text, $id, $args, bool $require = false,bool $multi = false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "list";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->vars[$this->lastTitle][$this->lastnum]["multi"] = $multi;
        $i = 0;
        foreach ($args as $arg) {

            $this->vars[$this->lastTitle][$this->lastnum]["option"][$i] = $arg;
            $i++;
        }

        $this->lastnum++;
    }

    public function addInput($title, $text, $id, $value = "",bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "input";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }

    public function addPassword($title, $text, $id, $value = "",bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "password";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }

    public function addUpload($title, $text, $id,$type = "*",bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "upload";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["type"] = $type;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }

    public function addNumber($title, $text, $id, $value = 0, $step = 1, $min = 0, $max = 10000000,bool $require=false,bool $readonly =false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "number";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["step"] = $step;
        $this->vars[$this->lastTitle][$this->lastnum]["min"] = $min;
        $this->vars[$this->lastTitle][$this->lastnum]["max"] = $max;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->vars[$this->lastTitle][$this->lastnum]["readonly"] = $readonly;
        $this->lastnum++;
    }


    public function addRage($title, $text, $id, $value = 0, $step = 1, $min = 0, $max = 10000,bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "rage";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["step"] = $step;
        $this->vars[$this->lastTitle][$this->lastnum]["min"] = $min;
        $this->vars[$this->lastTitle][$this->lastnum]["max"] = $max;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }

    public function addHidden($id, $value = "",bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "hidden";
        $this->vars[$this->lastTitle][$this->lastnum]["value"] = $value;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }


    public function addCheck($title, $text, $value1, $value2, $id,bool $require=false): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "check";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["value1"] = $value1;
        $this->vars[$this->lastTitle][$this->lastnum]["value2"] = $value2;
        $this->vars[$this->lastTitle][$this->lastnum]["check"] = false;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["require"] = $require;
        $this->lastnum++;
    }

    public function setCheck($checked = false): void
    {
        $this->vars[$this->lastTitle][$this->lastnum - 1]["check"] = $checked;
    }


    public function addButton($value1, $id, $value2,$extra = ""): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "button";
        $this->vars[$this->lastTitle][$this->lastnum]["value1"] = $value1;
        $this->vars[$this->lastTitle][$this->lastnum]["value2"] = $value2;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->vars[$this->lastTitle][$this->lastnum]["extra"] = $extra;
        $this->lastnum++;
    }


    public function addText($text): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "text";
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = "";
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->lastnum++;
    }


    public function addCalender($title,$text,$id): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "calender";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum++;
    }


    public function addTextTemplate($toid, $text, $name, $id): void
    {

        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "texttemplate";
        $this->vars[$this->lastTitle][$this->lastnum]["name"] = $name;
        $this->vars[$this->lastTitle][$this->lastnum]["toid"] = $toid;
        $this->vars[$this->lastTitle][$this->lastnum]["text"] = $text;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum++;
    }

    public function addSubmit($title, $id): void
    {
        $this->vars[$this->lastTitle][$this->lastnum]["art"] = "submit";
        $this->vars[$this->lastTitle][$this->lastnum]["title"] = $title;
        $this->vars[$this->lastTitle][$this->lastnum]["id"] = $id;
        $this->lastnum++;
    }


    public function show(): string
    {

        $r = '<form style="' . $this->style . '" method="' . $this->method . '" enctype="multipart/form-data" action="' . $this->url . '" >';
        foreach ($this->vars as $key => $value) {

            $r .= '<section class="section">';
            if ($key !== "") {
                $r .= '<h2 class="sectionTitle">' . $key . '</h2>';
            }
            foreach ($value as $var) {

                $id = $var["id"]??"";
                $style = "";
                $extra = "";
                if (isset($var["style"])) {
                    $style = ' style="' . $var["style"] . '" ';
                }
                if (isset($var["extra"])) {
                    $extra = ' ' . $var["extra"] . ' ';
                }


                if ($var["art"] === "button") {
                    if($extra === "disabled"){
                        $r .= '<button ' . $style . ' ' . $extra . ' class="buttondanger" accesskey="s" id="' . $id . '" value="' . $var["value2"] . '" name="' . $id . '" data-type="save">' . $var["value1"] . '</button>';
                        continue;
                    }
                    $r .= '<button ' . $style . ' ' . $extra . ' class="buttonPrimary" accesskey="s" id="' . $id . '" value="' . $var["value2"] . '" name="' . $id . '" data-type="save">' . $var["value1"] . '</button>';
                    continue;
                }
                if ($var["art"] === "submit") {
                    $r .= '<input ' . $style . ' ' . $extra . ' type="submit" id="' . $id . '" name="' . $id . '" placeholder="'.$var["title"].'"  class="medium">';
                    continue;
                }
                if(isset($var["title"])) {
                    $r .= '<dl class="' . $id . 'Input">';
                    $r .= '<dt><label for="' . $id . '">' . $var["title"] . '</label></dt>';
                    $r .= '<dd>';
                }
                if ($var["art"] === "calender") {
                    $r .= '<input ' . $style . ' ' . $extra . ' id="' . $id . '" name="' . $id . '" class="calendersty" type="datetime-local" value="'.date("y-m-d").'"/>';
                }
                if ($var["art"] === "texttemplate") {

                    $r .= '<input ' . $style . ' type="button" name="' . $id . '" value="' . $var["name"] . '" onClick="setText(\'' . $var["toid"] . '\', \'' . $var["text"] . '\');' . $extra . '">';
                    continue;
                }
                if ($var["art"] === "text") {

                    $r .= $var["text"];
                    continue;
                }
                if ($var["art"] === "hidden") {

                    $r .= '<input ' . $style . ' ' . $extra . ' type="hidden" id="' . $id . '" name="' . $id . '" value="' . $var["value"] . '">';
                    continue;
                }




                $required = ($var["require"] ?? false) ? "required" : "";
                $readonly = ($var["readonly"] ?? false) ? "readonly" : "";

                if ($var["art"] === "input") {

                    $r .= '<input ' . $style . ' ' . $extra . ' '.$required.' type="text" id="' . $id . '" name="' . $id . '" value="' . $var["value"] . '" class="medium">';
                } else if ($var["art"] === "password") {
                    $r .= '<input ' . $style . ' ' . $extra . ' '.$required.' type="password" id="' . $id . '" name="' . $id . '" value="' . $var["value"] . '" class="medium">';
                } else if ($var["art"] === "upload") {
                    $type = ($var["type"] !== "*") ? 'accept="'.$var["type"].'"' : "";
                    $r .= '<input ' . $style . ' ' . $extra . ' '.$type.' '.$required.' style="border: 2px dashed #e0e4e8;padding: 30px;padding-left: 200px;padding-right: 200px;" type="file" name="' . $id . '"  id="' . $id . '" accept="image/png">';
                } else if ($var["art"] === "number") {
                    $r .= '<input ' . $style . ' ' . $extra . ' '.$readonly. ' '.$required.' type="number" id="' . $id . '" name="' . $id . '" min="' . $var["min"] . '" max="' . $var["max"] . '" step="' . $var["step"] . '" value="' . $var["value"] . '" class="medium">';
                } else if ($var["art"] === "rage") {
                    $r .= '<input ' . $style . ' ' . $extra . ' '.$required.' type="range" id="' . $id . '" name="' . $id . '" min="' . $var["min"] . '" max="' . $var["max"] . '" step="' . $var["step"] . '" value="' . $var["value"] . '" class="medium">';
                } else if ($var["art"] === "list") {
                    $multi = ($var["multi"] ?? false) ? "multiple" : "";
                    $r .= '<input ' . $style . ' ' . $extra . ' type="text" id="' . $id . '" name="' . $id . '" list="list_' . $id . '" class="medium" '.$multi.'>';
                    $r .= '<datalist id="list_' . $id . '">';

                    foreach ($var["option"] as $k) {

                        $r .= '<option value="' . $k . '">';
                    }

                    $r .= '</datalist>';

                } else if ($var["art"] === "textarea") {
                    $r .= '<textarea ' . $style . ' ' . $extra . ' id="' . $id . '" name="' . $id . '" cols="40" rows="5" >' . $var["value"] . '</textarea>';
                }  else if ($var["art"] === "select") {
                    $multi = ($var["multi"] ?? false) ? "multiple" : "";
                    $r .= '<select ' . $style . ' ' . $extra . ' id="' . $id . '" name="' . $id . '" '.$multi.'>';
                    $true = true;
                    foreach (($var["option"]) as $k) {

                        if (isset($var["select"])) {
                            if ($var["select"] === $k) {
                                $r .= '<option value="' . $k . '" selected="">' . $k . '</option>';
                            } else {
                                $r .= '<option value="' . $k . '">' . $k . '</option>';
                            }
                        } else
                            if ($true) {

                                $true = false;
                                $r .= '<option value="' . $k . '" selected="">' . $k . '</option>';
                            } else {

                                $r .= '<option value="' . $k . '">' . $k . '</option>';
                            }

                    }

                    $r .= '</select>';
                } else if ($var["art"] === "check") {
                    if ($var["check"]) {
                        $r .= '<ol ' . $style . ' ' . $extra . ' class="flexibleButtonGroup optionTypeBoolean">';
                        $r .= '<li>';
                        $r .= '  <input type="radio" id="check' . $id . '" checked="" name="' . $id . '" value="' . $var["value1"] . '">';
                        $r .= '  <label for="check' . $id . '" class="green">';
                        $r .= '  <span class="icon icon16 fa-check"></span> ';
                        $r .= '  ' . $var["value1"] . '</label>';
                        $r .= '</li>';
                        $r .= '<li>';
                        $r .= '  <input type="radio" id="checkno' . $id . '" name="' . $id . '" value="' . $var["value2"] . '">';
                        $r .= '  <label for="checkno' . $id . '" class="red">';
                        $r .= '  <span class="icon icon16 fa-times"></span> ';
                        $r .= $var["value2"] . '</label>';
                        $r .= '</li>';
                        $r .= '</ol>';
                    } else {
                        $r .= '<ol ' . $style . ' ' . $extra . ' class="flexibleButtonGroup optionTypeBoolean">';
                        $r .= '<li>';
                        $r .= '  <input type="radio" id="check' . $id . '" name="' . $id . '" value="' . $var["value1"] . '">';
                        $r .= '  <label for="check' . $id . '" class="green">';
                        $r .= '  <span class="icon icon16 fa-check"></span> ';
                        $r .= '  ' . $var["value1"] . '</label>';
                        $r .= '</li>';
                        $r .= '<li>';
                        $r .= '  <input type="radio" id="checkno' . $id . '" checked="" name="' . $id . '" value="' . $var["value2"] . '">';
                        $r .= '  <label for="checkno' . $id . '" class="red">';
                        $r .= '  <span class="icon icon16 fa-times"></span> ';
                        $r .= $var["value2"] . '</label>';
                        $r .= '</li>';
                        $r .= '</ol>';
                    }
                }

                if(isset($var["title"])) {
                    $r .= '<small>' . $var["text"] . '</small>';
                    $r .= '</dd>';
                    $r .= '</dl>';
                }
            }

            $r .= '</section>';
        }

        $r .= '</form>';

        return $r;
    }


}

?>