<?php 

require_once("vendor/autoload.php");


//use Hcode\DB\Sql;
use \Slim\Slim;
use \Hcode\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	//echo "OK";

	$page=new Page();

	$page->setTlp("index");

	

});

$app->run();//dps dde carregado os arquivos ele roda

 ?>