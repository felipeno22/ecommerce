<?php


use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

//rota tela  de lista de categorias
$app->get('/admin/categories',function (){

	User::verifyLogin();

	$categories=Category::listAll();
	
	$admin=new PageAdmin();

 	$admin->setTlp("categories",array("categories"=>$categories));

});


//chama tela de criar categorias
$app->get("/admin/categories/create", function () {

	User::verifyLogin();

 	
 	$admin= new PageAdmin();

 	$admin->setTlp("categories-create");


 	
});

//rota tela  de lista de categorias
$app->post('/admin/categories/create',function (){


	User::verifyLogin();
 		
 	$category = new Category();
 	$category->setData($_POST);

 	$category->save();

 	header("Location: /admin/categories");
 	exit;

});


//chama tela de alterar categoria
$app->get("/admin/categories/:idcategory", function ($idcategory) {

 	User::verifyLogin();

	$categories = new Category();

	$categories->get((int)$idcategory);//convertendo o id passado para int 	


	$admin=new PageAdmin();

 	$admin->setTlp("categories-update",array("category"=>$categories->getValues()));

});




//rota para alterar categorias
$app->post("/admin/categories/:idcategory", function ($idcategory) {


 	User::verifyLogin();

	$categories = new Category();

	$categories->get((int)$idcategory);//convertendo o id passado para int 	

	$categories->setData($_POST);
	$categories->update();

 	header("Location: /admin/categories");
 	exit;




});


$app->get("/admin/categories/:idcategory/delete", function ($idcategory) {

 	User::verifyLogin();


	$categories = new Category();

	

	

	
	$categories->delete($idcategory);

 	header("Location: /admin/categories");
 	exit;


});




$app->get("/admin/categories/:idcategory/products", function ($idcategory) {

 	User::verifyLogin();


	$categories = new Category();

	

	$categories->get((int)$idcategory);//convertendo o id passado para int 	

 	$admin= new PageAdmin();

 	$admin->setTlp("categories-products",["category"=>$categories->getValues(),
	 											"productsRelated"=>$categories->getProducts(),
 												"productsNotRelated"=>$categories->getProducts(false)]);



});



$app->get("/admin/categories/:idcategory/products/:idproduto/add", function ($idcategory,$idproduct) {

 	User::verifyLogin();


	$categories = new Category();

	

	$categories->get((int)$idcategory);//convertendo o id passado para int 	

 	$products=new Product();

	$products->get((int)$idproduct);

 	$categories->addProduct($products);

	header("Location: /admin/categories/".$idcategory."/products");
 	exit;
 	

});


$app->get("/admin/categories/:idcategory/products/:idproduto/remove", function ($idcategory,$idproduct) {

 	User::verifyLogin();


	$categories = new Category();

	

	$categories->get((int)$idcategory);//convertendo o id passado para int 	

 	$products=new Product();

	$products->get((int)$idproduct);

 	$categories->removeProduct($products);

	header("Location: /admin/categories/".$idcategory."/products");
 	exit;
 	

});


?>