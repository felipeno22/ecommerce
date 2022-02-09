<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;


/*
$app->get('/', function() {
    
	$page=new Page();

	$page->setTlp("index");
	

});

*/

$app->get('/', function() {

	$products= Product::listAll();
    
	$page=new Page();
	//$p=new Product();
	$arr=Product::checkList($products);

	$a=[];

	foreach ($arr as $key) {
		$b=[];
		$b['idproduct']=$key->getIdproduct();
		$b['desproduct']=$key->getDesproduct();
		$b['vlprice']=$key->getVlprice();
		$b['vlwidth']=$key->getVlwidth();
		$b['vlheight']=$key->getVlheight();
		$b['vllength']=$key->getVllength();
		$b['vlweight']=$key->getVlweight();
		$b['desurl']=$key->getDesurl();
		$b['desphoto']=$key->getDesphoto();

		$a[]=$b;
	}
	

	$page->setTlp("index",["products"=>$a]);
	

});



//rota tela  de lista de categorias
$app->get('/categories/:idcategory',function ($idcategory){

	$categories=new Category();

	$categories->get((int)$idcategory);
	
	
	$page=new Page();

	$arr=Product::checkList($categories->getProducts());

	$a=[];

	foreach ($arr as $key) {
		$b=[];
		$b['idproduct']=$key->getIdproduct();
		$b['desproduct']=$key->getDesproduct();
		$b['vlprice']=$key->getVlprice();
		$b['vlwidth']=$key->getVlwidth();
		$b['vlheight']=$key->getVlheight();
		$b['vllength']=$key->getVllength();
		$b['vlweight']=$key->getVlweight();
		$b['desurl']=$key->getDesurl();
		$b['desphoto']=$key->getDesphoto();

		$a[]=$b;
	}
	

	

 	$page->setTlp("category",array("idcategory"=>$categories->getIdcategory(),"descategory"=>$categories->getDescategory(),"products"=>$a));

});










?>