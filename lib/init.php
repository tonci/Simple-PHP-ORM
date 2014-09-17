<?php
function __autoload($class_name) {
    if (is_file(dirname(__FILE__)."/".$class_name . '.php')){
        include_once(dirname(__FILE__)."/".$class_name . '.php');
        return;
    }
}