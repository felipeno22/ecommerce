<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;


/*
$app->get('/', function() {
    
	$page=new Page();

	$page->setTlp("index");
	

});

*/

$app->get('/', function() {

	$products= Product::listAll();
    

	$page = new Page();

	$page->setTlp("index", [
		'products'=>Product::checkList($products)
	]);
	

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


	

 	$page->setTlp("category",array("idcategory"=>$categories->getIdcategory(),"descategory"=>$categories->getDescategory(),"products"=>$pagination['data'],"pages"=>$pages));

});



$app->get('/products/:desurl',function ($desurl){


		$products=new  Product();

		$products->getFromURL($desurl);

		$page=new Page();

		$page->setTlp("product-detail",array("products"=>$products->getValues(),
 									"categories"=>$products->getCategories()));




});


$app->get("/cart", function (){

	$cart=Cart::getFromSession();


	$page =new Page();


	$page->setTlp("cart",["cart"=>$cart->getValues(),
					"products"=>$cart->getProducts(),
					'error'=>Cart::getMsgError()]);

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

		User::verifyLogin(false);

		$address=new Address();
		$cart= Cart::getFromSession();

		
		//caso não tiver definido o cep
		if (!isset($_GET['zipcode'])) {

			//atribua o cep dp  banco ao formulario
		$_GET['zipcode'] = $cart->getdeszipcode();

	}

	if (isset($_GET['zipcode'])) {

		$address->loadFromCEP($_GET['zipcode']);

		$cart->setdeszipcode($_GET['zipcode']);

		$cart->save();

		$cart->getCalculateTotal();

	}


	//caso os objetos nao tenha nada atribui vazio
	if (!$address->getdesaddress()) $address->setdesaddress('');
	if (!$address->getdesnumber()) $address->setdesnumber('');
	if (!$address->getdescomplement()) $address->setdescomplement('');
	if (!$address->getdesdistrict()) $address->setdesdistrict('');
	if (!$address->getdescity()) $address->setdescity('');
	if (!$address->getdesstate()) $address->setdesstate('');
	if (!$address->getdescountry()) $address->setdescountry('');
	if (!$address->getdeszipcode()) $address->setdeszipcode('');


		
		$page=new Page();

		$page->setTlp("checkout",["cart"=>$cart->getValues(),
		"address"=>$address->getValues(),'products'=>$cart->getProducts(),'error'=>Address::getMsgError()]);


});



$app->post("/checkout", function(){

	User::verifyLogin(false);

	if (!isset($_POST['zipcode']) || $_POST['zipcode'] === '') {
		Address::setMsgError("Informe o CEP.");
		header('Location: /checkout');
		exit;
	}

	if (!isset($_POST['desaddress']) || $_POST['desaddress'] === '') {
		Address::setMsgError("Informe o endereço.");
		header('Location: /checkout');
		exit;
	}

	if (!isset($_POST['desdistrict']) || $_POST['desdistrict'] === '') {
		Address::setMsgError("Informe o bairro.");
		header('Location: /checkout');
		exit;
	}

	if (!isset($_POST['descity']) || $_POST['descity'] === '') {
		Address::setMsgError("Informe a cidade.");
		header('Location: /checkout');
		exit;
	}

	if (!isset($_POST['desstate']) || $_POST['desstate'] === '') {
		Address::setMsgError("Informe o estado.");
		header('Location: /checkout');
		exit;
	}

	if (!isset($_POST['descountry']) || $_POST['descountry'] === '') {
		Address::setMsgError("Informe o país.");
		header('Location: /checkout');
		exit;
	}

	$user = User::getFromSession();

	$address = new Address();

	$_POST['deszipcode'] = $_POST['zipcode'];
	$_POST['idperson'] = $user->getidperson();

	$address->setData($_POST);

	$address->save();


	$cart = Cart::getFromSession();

	$cart->getCalculateTotal();

	$order = new Order();

	$order->setData([
		'idcart'=>$cart->getidcart(),
		'idaddress'=>$address->getidaddress(),
		'iduser'=>$user->getiduser(),
		'idstatus'=>OrderStatus::EM_ABERTO,
		'vltotal'=>$cart->getvltotal()
	]);

	

	$order->save();

/*	switch ((int)$_POST['payment-method']) {

		case 1:
		header("Location: /order/".$order->getidorder()."/pagseguro");
		break;

		case 2:
		header("Location: /order/".$order->getidorder()."/paypal");
		break;

	}
*/
	header("Location: /order/".$order->getidorder());
	exit;



});





$app->get("/order/:idorder", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();

	$page = new Page();

	$page->setTlp("payment", [
		'order'=>$order->getValues()
	]);


});


$app->get("/boleto/:idorder", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	// DADOS DO BOLETO PARA O SEU CLIENTE
	$dias_de_prazo_para_pagamento = 10;
	$taxa_boleto = 5.00;
	$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 

	$valor_cobrado = formatPrice($order->getvltotal()); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
	$valor_cobrado = str_replace(".", "", $valor_cobrado);
	$valor_cobrado = str_replace(",", ".",$valor_cobrado);
	$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

	$dadosboleto["nosso_numero"] = $order->getidorder();  // Nosso numero - REGRA: Máximo de 8 caracteres!
	$dadosboleto["numero_documento"] = $order->getidorder();	// Num do pedido ou nosso numero
	$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
	$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
	$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

	// DADOS DO SEU CLIENTE
	$dadosboleto["sacado"] = $order->getdesperson();
	$dadosboleto["endereco1"] = $order->getdesaddress() . " " . $order->getdesdistrict();
	$dadosboleto["endereco2"] = $order->getdescity() . " - " . $order->getdesstate() . " - " . $order->getdescountry() . " -  CEP: " . $order->getdeszipcode();

	// INFORMACOES PARA O CLIENTE
	$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Hcode E-commerce";
	$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
	$dadosboleto["demonstrativo3"] = "";
	$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
	$dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
	$dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
	$dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto["quantidade"] = "";
	$dadosboleto["valor_unitario"] = "";
	$dadosboleto["aceite"] = "";		
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";


	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


	// DADOS DA SUA CONTA - ITAÚ
	$dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "48781";	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

	// DADOS PERSONALIZADOS - ITAÚ
	$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

	// SEUS DADOS
	$dadosboleto["identificacao"] = "Hcode Treinamentos";
	$dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
	$dadosboleto["endereco"] = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
	$dadosboleto["cidade_uf"] = "São Bernardo do Campo - SP";
	$dadosboleto["cedente"] = "HCODE TREINAMENTOS LTDA - ME";

	// NÃO ALTERAR!
	$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR;

	require_once($path . "funcoes_itau.php");
	require_once($path . "layout_itau.php");


});


/*

$app->get("/order/:idorder/pagseguro", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();

	$page = new Page([
		'header'=>false,
		'footer'=>false
	]);

	$page->setTpl("payment-pagseguro", [
		'order'=>$order->getValues(),
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts(),
		'phone'=>[
			'areaCode'=>substr($order->getnrphone(), 0, 2),
			'number'=>substr($order->getnrphone(), 2, strlen($order->getnrphone()))
		]
	]);


});

$app->get("/order/:idorder/paypal", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();

	$page = new Page([
		'header'=>false,
		'footer'=>false
	]);

	$page->setTpl("payment-paypal", [
		'order'=>$order->getValues(),
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts()
	]);


});*/



$app->get("/login", function (){

	
		$page=new Page();


		$page->setTlp("login",["error"=>User::getMsgError(),'errorRegister'=>User::getErrorRegister(),
	'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=>'', 'email'=>'', 'phone'=>'']]);


	

});


$app->post("/login", function (){

		try{

			User::login($_POST['login'],$_POST['password']);
			User::setMsgError('');

		}catch(Exception $e){

		

			User::setMsgError($e->getMessage());

		

		}
	
		
		
		header("Location: /checkout");
		exit;



});



$app->get("/logout", function (){

	User::logout();
	header("Location: /login");
		exit;

	

});




$app->post("/register", function(){

	$_SESSION['registerValues'] = $_POST;

	if (!isset($_POST['name']) || $_POST['name'] == '') {

		User::setErrorRegister("Preencha o seu nome.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['email']) || $_POST['email'] == '') {

		User::setErrorRegister("Preencha o seu e-mail.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['password']) || $_POST['password'] == '') {

		User::setErrorRegister("Preencha a senha.");
		header("Location: /login");
		exit;

	}

	if (User::checkLoginExist($_POST['email']) === true) {

			User::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
		header("Location: /login");
		exit;

	}

	$user = new User();


	
	$user->setValue($_POST);
	$user-> save();

	User::login($_POST['email'], $_POST['password']);

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
 	$user= User::getForgot($_POST["email"],false);

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
	$user= User::validForgotDecrypt($_GET['code']);

	$page=new Page();

 	$page->setTlp("forgot-reset",array("name"=>$user['desperson'],"code"=>$_GET['code']));
});


//rota aonde  faz a validação de senha novamente
$app->post('/forgot/reset',function (){


	//pegando o codigo para validar novamente
	$forgot= User::validForgotDecrypt($_POST['code']);

//setando data de   recuperação de senha
	User::setFogotUsed($forgot['idrecovery']);

	$user = new User();

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



$app->get('/profile',function (){



	User::verifyLogin(false);
	$user= User::getFromSession();



	$page=new Page();

 	$page->setTlp("profile",array("user"=>$user->getValues(),
 										"profileMsg"=>User::getSuccess(),"profileError"=>User::getMsgError()));

});



$app->post('/profile',function (){



	User::verifyLogin(false);
	
	if(!isset($_POST['desperson']) || $_POST['desperson']==='' ){
		User::setMsgError("Preencha o seu nome");
		header("Location: /profile");
 		exit();

	}

	if(!isset($_POST['desemail']) || $_POST['desemail']==='' ){
		User::setMsgError("Preencha o seu e-mail");
		header("Location: /profile");
 		exit();

	}


	$user= User::getFromSession();

	if($_POST['desemail'] !== $user->getDesemail()){

			if(User::checkLoginExist($_POST['desemail'])){

					User::setMsgError("Este endereço de e-mail ja está cadastrado.");
					header("Location: /profile");
 					exit();


			}

	}

	$_POST['inadmin']=$user->getInadmin();
	$_POST['password']=$user->getDespassword();
	$_POST['deslogin']= $_POST['desemail'];


	$user->setData($_POST);
	$user->update();

	 User::setSuccess("Dados alterados com sucesso!");
	
 	header("Location: /profile");
 	exit();
});






?>