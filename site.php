<?php 

use \Hcode\Page;
use \Hcode\Model\Categories;
use \Hcode\Model\Product;

$app->get('/', function() {
   
   $products = Product::listAll();

   $page = new Page();

   $page->setTpl("index",[
   	 "products"=>Product::checklist($products)
   ]);

});

$app->get("/categories/:idcategory",function($idcategory){

   $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$categories = new Categories();

	$categories->get((int)$idcategory);

   $pagination = $categories->getProductsPage($page);

   $pages = $categories->Pagination($pagination['pages']);

	$page = new Page();

   	$page->setTpl("category",[
   		"category"=>$categories->getValues(),
   		"products"=>$pagination['data'],
         "pages"=>$pages
   	]);
});


?>