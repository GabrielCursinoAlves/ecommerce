<?php 
session_start();

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

$app->config('debug', true);

require_once 'functions.php';
require_once 'site.php';
require_once 'admin.php';
require_once 'admin-user.php';
require_once 'admin-recover.php';
require_once 'admin-categories.php';
require_once 'admin-products.php';

$app->run();

?>