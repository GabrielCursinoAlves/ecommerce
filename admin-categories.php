<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;
use \Hcode\Model\Categories;

$app->get("/admin/categories",function(){

	User::verifylogin();

	$categories = Categories::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories",[
		"categories"=>$categories
	]);
});

$app->get("/admin/categories/create",function(){

	User::verifylogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");
});

$app->post("/admin/categories/create",function(){

	User::verifylogin();

	$categories = new Categories();

	$categories->setData($_POST);

	$categories->save();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategories/delete",function(
$idcategories){

	User::verifylogin();

	$categories = new Categories();

	$categories->get((int)$idcategories);

	$categories->delete();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategories",function($idcategories){

	User::verifylogin();

	$categories = new Categories();

	$categories->get((int)$idcategories);

	$page = new PageAdmin();

	$page->setTpl("categories-update",[
		"category"=>$categories->getValues()
	]);
});

$app->post("/admin/categories/:idcategories",function($idcategories){

	User::verifylogin();

	$categories = new Categories();

	$categories->get((int)$idcategories);

	$categories->setData($_POST);

	$categories->save();

	header("Location: /admin/categories");
	exit;
});

$app->get("/admin/categories/:idcategory/products",
function($idcategory){

	User::verifylogin();

	$categories = new Categories();

	$categories->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products",[
		"category"=>$categories->getValues(),
		"productsRelated"=>$categories->getProducts(),
		"productsNotRelated"=>$categories->getProducts(false)
	]);

});

$app->get("/admin/categories/:idcategory/products/:idproduct/add",
function($idcategory,$idproduct){

	User::verifylogin();

	$categories = new Categories();

	$categories->get((int)$idcategory);

	$products = new Product();

	$products->get((int)$idproduct);

	$categories->addProduct($products);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;

});

$app->get("/admin/categories/:idcategory/products/:idproduct/remove",
function($idcategory,$idproduct){

	User::verifylogin();

	$categories = new Categories();

	$categories->get((int)$idcategory);

	$products = new Product();

	$products->get((int)$idproduct);

	$categories->removeProduct($products);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
	
});

?>