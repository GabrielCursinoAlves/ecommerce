<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
   
   $page = new Page();

   $page->setTpl("index");

});

$app->get("/admin",function(){

	User::verifylogin();

	$page = new PageAdmin();

	$page->setTpl("index");
});

$app->get("/admin/login",function(){

	$page = new PageAdmin([
		'header'=>false,
		'footer'=>false
	]);

	$page->setTpl("login");
});

$app->post("/admin/login",function(){

	User::login($_POST['login'],$_POST['password']);

	header('Location: /admin');
	exit;
});

$app->get("/admin/logout",function(){

	User::logout();

	header("Location: /admin/login");
	exit;
});

$app->get("/admin/users",function(){

	User::verifylogin();

	$users = User::listall();

	$page = new PageAdmin();

	$page->setTpl("users",[
		"users"=>$users
	]);
});

$app->get("/admin/users/create",function(){

	User::verifylogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");
});

$app->get("/admin/users/:iduser/delete",function($iduser){
	
	User::verifylogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;


});

$app->get("/admin/users/:iduser",function($iduser){

	User::verifylogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update",[
		"user"=>$user->getValues()
	]);
});

$app->post("/admin/users/create",function(){

	User::verifylogin();

	$users = new User();

	$_POST['inadmin'] = (isset($_POST['inadmin'])) ? 1 : 0;

	$users->setData($_POST);

	$users->save();

	header("Location: /admin/users");
	exit;
});

$app->post("/admin/users/:iduser",function($iduser){
	
	User::verifylogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;
});

$app->run();

?>