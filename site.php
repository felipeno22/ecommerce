<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\Usuario;


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


$app->get("/checkout", function (){

		Usuario::verifyLogin(false);

		$cart= Cart::getFromSession();

		$address=new Address();

		
		$page=new Page();

		$page->setTlp("checkout",["idcart"=>$cart->getIdcart(),
			"iduser"=>$cart->getIduser(),
			"dessessionid"=>$cart->getDessessionid(),
			"deszipcode"=>$cart->getDeszipcode(),
			"vlfreight"=>$cart->getVlfreight(),
		"nrdays"=>$cart->getNrdays(),
		"desaddress"=>$address->getDesaddress(),
		"descomplement"=>$address->getDescomplement(),
		"desdistrict"=>$address->getDesdistrict(),
		"descity"=>$address->getDescity(),
		"desstate"=>$address->getDesstate(),
		"descountry"=>$address->getDescountry()]);


});


$app->get("/login", function (){

	
		$page=new Page();


		$page->setTlp("login",["error"=>Usuario::getMsgError(),'errorRegister'=>Usuario::getErrorRegister(),
	'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=>'', 'email'=>'', 'phone'=>'']]);


	

});


$app->post("/login", function (){

		try{

			Usuario::login($_POST['login'],$_POST['password']);

		}catch(Exception $e){

		

			Usuario::setMsgError($e->getMessage());

		

		}
	
		
		
		header("Location: /checkout");
		exit;



});



$app->get("/logout", function (){

	Usuario::logout();
	header("Location: /login");
		exit;

	

});




$app->post("/register", function(){

	$_SESSION['registerValues'] = $_POST;

	if (!isset($_POST['name']) || $_POST['name'] == '') {

		Usuario::setErrorRegister("Preencha o seu nome.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['email']) || $_POST['email'] == '') {

		Usuario::setErrorRegister("Preencha o seu e-mail.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['password']) || $_POST['password'] == '') {

		Usuario::setErrorRegister("Preencha a senha.");
		header("Location: /login");
		exit;

	}

	if (Usuario::checkLoginExist($_POST['email']) === true) {

			Usuario::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
		header("Location: /login");
		exit;

	}

	$user = new Usuario();


	$dados=array();
	/*
	$dados=['inadmin'=>'','deslogin'=>'','desperson'=>'','desemail'=>'','despassword'=>'','nrphone'=>''];*/


	

	$dados['inadmin']=0;
	$dados['deslogin']=$_POST['email'];
	$dados['desperson']=$_POST['name'];
	$dados['desemail']=$_POST['email'];
	$dados['despassword']=$_POST['password'];
	$dados['nrphone']=$_POST['phone'];

	

	$user-> save($dados);

	Usuario::login($_POST['email'], $_POST['password']);

	header('Location: /checkout');
	exit;

});





//rota tela de esqueceu senha (onde digita o email)
$app->get('/forgot',function (){


	//passando parametros para desativar footer e header 
	$page=new Page();

 	$page->setTlp("forgot");

});


//rota  para verificar se o existe usuario com o email digitado
$app->post('/forgot',function (){


	//enviando o email digitado para verificar se existe usuario com ele
 	$user= Usuario::getForgot($_POST["email"],false);

 		header("Location: /forgot/sent");
 		exit;

});


//rota para chamar a tela de  menssagem que o email foi enviado
$app->get('/forgot/sent',function (){


	$page=new Page();

 	$page->setTlp("forgot-sent");
});


//rota para chamar tela de digitar nova senha
//nessa tela ja ocorre a validação  da recupeção de senha
$app->get('/forgot/reset',function (){

	//pegando o codigo para validar
	$user= Usuario::validForgotDecrypt($_GET['code']);

	$page=new Page();

 	$page->setTlp("forgot-reset",array("name"=>$user['desperson'],"code"=>$_GET['code']));
});


//rota aonde  faz a validação de senha novamente
$app->post('/forgot/reset',function (){


	//pegando o codigo para validar novamente
	$forgot= Usuario::validForgotDecrypt($_POST['code']);

//setando data de   recuperação de senha
	Usuario::setFogotUsed($forgot['idrecovery']);

	$user = new Usuario();

	//pegando dados do usario
	$user->get((int)$forgot['iduser']);//convertendo o id passado para int 

//password transforma senha de caracteres em hash para salvar no banco
	//o terceiro param é um array aonde vc define o numero de processamente
	//coloque sempre 12 para haver equilibrio
	//$password=password_hash($_POST['password'], PASSWORD_DEFAULT,['cost'=>12]);
	$password=md5($_POST['password']);
	//alterando a senha do usuario
	$user->setPassword($password);	

	$page=new Page();

 	$page->setTlp("forgot-reset-success");
});




?>