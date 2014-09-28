<?php
function __autoload($class_name) {
    $folders = array(dirname(__FILE__), dirname(__FILE__).'/validators');
    foreach ($folders as $folder) {
        if (is_file($folder."/".$class_name . '.php')){
            include_once($folder."/".$class_name . '.php');
            return;
        }
    }
}