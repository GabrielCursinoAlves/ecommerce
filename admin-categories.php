<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
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


?>