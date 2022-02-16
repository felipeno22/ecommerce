<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;


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

	//se nao houver um numero de pagina definido sera por padrão 1
	$p=(isset($_GET['page'])) ? (int)$_GET["page"] : 1;


	$categories=new Category();

	$categories->get((int)$idcategory);
	
	$pages=[];

	//passando o num de paginas  para fazer a paginaçao
//o num de item por pagina nao esta sendo passado por param
//entao por padrão e  3 	
	$pagination=$categories->getProductsPage($p);



	for ($i=1; $i<= $pagination['totalPages'];$i++) {

		array_push($pages,["link"=>"/categories/".$categories->getIdcategory()."?page=".$i,
							"page"=>$i]);
	}

	$page=new Page();



	//$arr=Product::checkList($categories->getProducts());
	$arr=$pagination['data'];
	
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
	

	

 	$page->setTlp("category",array("idcategory"=>$categories->getIdcategory(),"descategory"=>$categories->getDescategory(),"products"=>$a,"pages"=>$pages));

});



$app->get('/products/:desurl',function ($desurl){


		$products=new  Product();

		$products->getFromURL($desurl);

		$page=new Page();

		$page->setTlp("product-detail",array("idproduct"=>$products->getIdproduct(),
 									"desproduct"=>$products->getDesproduct(),
 									"vlprice"=>$products->getVlprice(),
 									"vlwidth"=>$products->getVlwidth(),
 									"vlheight"=>$products->getVlheight(),
 									"vllength"=>$products->getVllength(),
 									"vlweight"=>$products->getVlweight(),
 									"desurl"=>$products->getDesurl(),
 									"desphoto"=>$products->getDesphoto(),
 									"categories"=>$products->getCategories()));




});


$app->get("/cart", function (){

	$cart=Cart::getFromSession();


	$page =new Page();

	
	
	$cart->getCalculateTotal();


	$page->setTlp("cart",["idcart"=>$cart->getIdcart(),
			"iduser"=>$cart->getIduser(),
			"dessessionid"=>$cart->getDessessionid(),
			"deszipcode"=>$cart->getDeszipcode(),
			"vlfreight"=>$cart->getVlfreight(),
			"nrdays"=>$cart->getNrdays(),
			"vlsubtotal"=>$cart->getVlsubtotal(),
			"vltotal"=>$cart->getVltotal(),
			"products"=>$cart->getProducts(),'error'=>Cart::getMsgError()]);

});


$app->get("/cart/:idproduct/add", function ($idproduct){


	$products=new Product();

	$products->get((int)$idproduct);



	$cart=Cart::getFromSession();

	$quantity=(isset($_GET['quantity']))? (int)$_GET['quantity']:1;

	for ($i=0; $i < $quantity ; $i++) { 
				$cart->addProducts($products);

			}

	


	header("Location: /cart");
	exit;
});


$app->get("/cart/:idproduct/minus", function ($idproduct){


	$products=new Product();

	$products->get((int)$idproduct);



	$cart=Cart::getFromSession();

	$cart->removeProducts($products);


	header("Location: /cart");
	exit;
});


$app->get("/cart/:idproduct/remove", function ($idproduct){


	$products=new Product();

	$products->get((int)$idproduct);



	$cart=Cart::getFromSession();

	$cart->removeProducts($products,true);


	header("Location: /cart");
	exit;
});


$app->post("/cart/freight", function (){


		$cart= Cart::getFromSession();



		$cart->setFreight($_POST['zipcode']);

		header("Location: /cart");
		exit;

});


?>