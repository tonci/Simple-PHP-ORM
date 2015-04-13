<?php
function __autoload($class_name) {
    $folders = array(dirname(__FILE__));
    foreach ($folders as $folder) {
        $class_name = str_replace('\\', '/', $class_name);
        if (is_file($folder."/".$class_name . '.php')){
            include_once($folder."/".$class_name . '.php');
            return;
        }
    }
}