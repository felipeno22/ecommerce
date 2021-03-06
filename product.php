<?php


use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

//rota tela  de lista de categorias
$app->get('/admin/products',function (){

	User::verifyLogin();

	//$products=Product::listAll();

	$search= (isset($_GET['search'])) ? $_GET['search'] :'';
 	$page= (isset($_GET['page'])) ? (int)$_GET['page'] :1;

 
 		if($search != ''){

 		$products=Product:: getPageSearch($search,$page);	

 		}else{


 		$products=Product:: getPage($page);

 		}

 		$pages=[];

for ($x=0; $x < $products['totalPages'] ; $x++) { 

	array_push($pages, ["href"=>"/admin/products?".http_build_query(["page"=>$x+1,"search"=>$search]),
						"text"=>$x+1]);
}
 	

	
	$admin=new PageAdmin();

 	$admin->setTlp("products",array("products"=>$products['data'], "search"=>$search,
 									"pages"=>$pages));

});

//chama tela de criar categorias
$app->get("/admin/products/create", function () {

	User::verifyLogin();

 	
 	$admin= new PageAdmin();

 	$admin->setTlp("products-create");


 	
});

//rota tela  de lista de categorias
$app->post('/admin/products/create',function (){


	User::verifyLogin();
 		
 	$products = new Product();

 	$products->setData($_POST);

 	$products->save();

 	header("Location: /admin/products");
 	exit;

});


//chama tela de alterar categoria
$app->get("/admin/products/:idproducts", function ($idproduct) {

 	User::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);//convertendo o id passado para int 	

	$products->checkPhoto();//chamando metodo de verificação de foto

	

	
	$admin=new PageAdmin();

 	$admin->setTlp("products-update",array("products"=>$products->getValues()));

});




//rota para alterar categorias
$app->post("/admin/products/:idproduct", function ($idproduct) {


 	User::verifyLogin();

	$products = new Product();

	$products->get((int)$idproduct);//convertendo o id passado para int 	

	$products->setData($_POST);
	
	$products->update();

	$products->changePhoto($_FILES['file']);//passando o nome do atributo name da tag de  upload

 	header("Location: /admin/products");
 	exit;




});


$app->get("/admin/products/:idproduct/delete", function ($idproduct) {

 	User::verifyLogin();


	$products = new Product();

	
	$products->delete($idproduct);

 	header("Location: /admin/products");
 	exit;


});


?>