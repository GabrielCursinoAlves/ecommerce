<?php 

use \Hcode\Page;
use \Hcode\Model\Product;

$app->get('/', function() {
   
   $products = Product::listAll();

   $page = new Page();

   $page->setTpl("index",[
   	 "products"=>Product::checklist($products)
   ]);

});

$app->get("/categories/:idcategory",function($idcategory){

	$categories = new Categories();

	$categories->get((int)$idcategory);

	$page = new Page();

   	$page->setTpl("category",[
   		"category"=>$categories->getValues(),
   		"products"=>[]
   	]);
});


?>