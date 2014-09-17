<?php
require_once "lib/init.php";
$company = new Company;
//print_r($company->findById(1));
//print_r($company->findAll('name=:name',array(':name'=>'Izdirvam.bg')));
//print_r($company->findAll());
$company->id = 123;
print_r($company);