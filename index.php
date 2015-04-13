<?php
require_once "src/init.php";

use SimpleORM\Product;

$product = new Product;
// $product->product_name = 'New mega cool product';
// $product->id = 12.5;
// $product->product_name = 'qwertyuiop qwertyuiop qwertyuiop qwertyuiop qwertyuiop';
// $product->product_discount = 'asd';
// $product->save();

print_r($product->findAll());