<?php


use \Hcode\PageAdmin;
use \Hcode\Model\Usuario;
use \Hcode\Model\Product;

//rota tela  de lista de categorias
$app->get('/admin/products',function (){

	Usuario::verifyLogin();

	$products=Product::listAll();

	
	
	$admin=new PageAdmin();

 	$admin->setTlp("products",array("products"=>$products));

});

//chama tela de criar categorias
$app->get("/admin/products/create", function () {

	Usuario::verifyLogin();

 	
 	$admin= new PageAdmin();

 	$admin->setTlp("products-create");


 	
});

//rota tela  de lista de categorias
$app->post('/admin/products/create',function (){


	Usuario::verifyLogin();
 		
 	$products = new Product();

 	$products->save($_POST);

 	header("Location: /admin/products");
 	exit;

});


//chama tela de alterar categoria
$app->get("/admin/products/:idproducts", function ($idproduct) {

 	Usuario::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);//convertendo o id passado para int 	

	$products->checkPhoto();//chamando metodo de verificação de foto

	

	
	$admin=new PageAdmin();

 	$admin->setTlp("products-update",array("idproduct"=>$products->getIdproduct(),
 									"desproduct"=>$products->getDesproduct(),
 									"vlprice"=>$products->getVlprice(),
 									"vlwidth"=>$products->getVlwidth(),
 									"vlheight"=>$products->getVlheight(),
 									"vllength"=>$products->getVllength(),
 									"vlweight"=>$products->getVlweight(),
 									"desurl"=>$products->getDesurl(),
 									"desphoto"=>$products->getDesphoto()));

});




//rota para alterar categorias
$app->post("/admin/products/:idproduct", function ($idproduct) {


 	Usuario::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);//convertendo o id passado para int 	


	$products->update($_POST,$idproduct);

	$products->changePhoto($_FILES['file']);//passando o nome do atributo name da tag de  upload

 	header("Location: /admin/products");
 	exit;




});


$app->get("/admin/products/:idproduct/delete", function ($idproduct) {

 	Usuario::verifyLogin();


	$products = new Product();

	
	$products->delete($idproduct);

 	header("Location: /admin/products");
 	exit;


});


?>