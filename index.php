<?php
require_once "lib/init.php";
$orm = new Company;
//print_r($orm->findById(1));
print_r($orm->find('name=:name',array(':name'=>'Chaos Group')));