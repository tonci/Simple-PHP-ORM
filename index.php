<?php
require_once "lib/init.php";

$product = new Product;
// $product->product_name = 'New mega cool product';
/* $product->save(); */
$product->validate(new DBValidator);
exit;
print_r($product->findAll());

$company = new Company;
print_r($company->findAll('name=:name',array(':name'=>'Izdirvam.bg')));
//print_r($company->findAll());

// $company->id = 123;
// print_r($company);

//delete by id
//print_r($company->delete(4));

//$company->delete('name=:name AND phone=:phone', array(':name'=>'test',':phone'=>123456));

//print_r($company->findByNameAndPhoneAndEmail('Izdirvam.bg', '0883321415', 'contact@izdirvam.bg'));

//print_r($company->find('name=:name', array(':name'=>'test')));
//$company->delete();

// INSERT
//$company->id = 11;
// $company->name = "teste2";
// $company->email = 'teste@3terst123.com';
// $company->phone = '088634823734';
// print_r($company->save());


//TEST ADDRESS
//$address = new Address;
//print_r($address->findByCountry('Bulgaria'));

//insert
// $address->country = 'Bulgaria';
// $address->street_name = 'Ivan Asen';
// $address->street_number = 54;
// $address->street_number_addition = 'a';
// $address->zip_id = 1;
// $address->save();

//delete
//$address->delete('street_number=:number', array(':number'=>54));
