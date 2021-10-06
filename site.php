<?php 

use \Hcode\Page;

$app->get('/', function() {
   
   $page = new Page();

   $page->setTpl("index");

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