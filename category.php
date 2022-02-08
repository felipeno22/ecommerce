<?php


use \Hcode\PageAdmin;
use \Hcode\Model\Usuario;
use \Hcode\Model\Category;

//rota tela  de lista de categorias
$app->get('/admin/categories',function (){

	Usuario::verifyLogin();

	$categories=Category::listAll();
	
	$admin=new PageAdmin();

 	$admin->setTlp("categories",array("categories"=>$categories));

});


//chama tela de criar categorias
$app->get("/admin/categories/create", function () {

	Usuario::verifyLogin();

 	
 	$admin= new PageAdmin();

 	$admin->setTlp("categories-create");


 	
});

//rota tela  de lista de categorias
$app->post('/admin/categories/create',function (){


	Usuario::verifyLogin();
 		
 	$category = new Category();

 	$category->save($_POST);

 	header("Location: /admin/categories");
 	exit;

});


//chama tela de alterar categoria
$app->get("/admin/categories/:idcategory", function ($idcategory) {

 	Usuario::verifyLogin();

	$categories = new Category();

	$categories->get((int)$idcategory);//convertendo o id passado para int 	


	$admin=new PageAdmin();

 	$admin->setTlp("categories-update",array("idcategory"=>$categories->getIdcategory(),
 									"descategory"=>$categories->getDescategory()));

});




//rota para alterar categorias
$app->post("/admin/categories/:idcategory", function ($idcategory) {


 	Usuario::verifyLogin();

	$categories = new Category();

	$categories->get((int)$idcategory);//convertendo o id passado para int 	

	//var_dump($user);
	$categories->update($_POST,$idcategory);

 	header("Location: /admin/categories");
 	exit;




});


$app->get("/admin/categories/:idcategory/delete", function ($idcategory) {

 	Usuario::verifyLogin();


	$categories = new Category();

	

	

	
	$categories->delete($idcategory);

 	header("Location: /admin/categories");
 	exit;


});


?>