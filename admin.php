<?php


use \Hcode\PageAdmin;
use \Hcode\Model\User;

//criando rota para a tela de  login
$app->get('/admin/login', function() {
    
	//echo "OK";

	//quando for a rota da tela de login header e footer da pagina é difente
	//entao passa os parametros como false para nao chamar os header e footer
	//que são padrões nas demais páginas
	$page=new PageAdmin(["header"=>false,"footer"=>false]);

	$page->setTlp("login");

	

});






//criando rota para a tela de  login
$app->post('/admin/login', function() {
  

    // chamando o métod de logar da classe User
	User::login($_POST['login'],$_POST['password']);

	

	//chamando a tela de adminstração após logar
	header("Location: /admin");
	exit;
	

});


//criando rota para a tela de  login
$app->get('/admin/logout', function() {
    

   User::logout();


	//chamando a tela de adminstração após logar
	header("Location: /admin/login");
	exit;
	

});






//rota tela de esqueceu senha (onde digita o email)
$app->get('/admin/forgot',function (){


	//passando parametros para desativar footer e header 
	$admin=new PageAdmin(["header"=>false,"footer"=>false]);

 	$admin->setTlp("forgot");

});


//rota  para verificar se o existe usuario com o email digitado
$app->post('/admin/forgot',function (){


	//enviando o email digitado para verificar se existe usuario com ele
 	$user= User::getForgot($_POST["email"]);

 		header("Location: /admin/forgot/sent");
 		exit;

});


//rota para chamar a tela de  menssagem que o email foi enviado
$app->get('/admin/forgot/sent',function (){


	$admin=new PageAdmin(["header"=>false,"footer"=>false]);

 	$admin->setTlp("forgot-sent");
});


//rota para chamar tela de digitar nova senha
//nessa tela ja ocorre a validação  da recupeção de senha
$app->get('/admin/forgot/reset',function (){

	//pegando o codigo para validar
	$user= User::validForgotDecrypt($_GET['code']);

	$admin=new PageAdmin(["header"=>false,"footer"=>false]);

 	$admin->setTlp("forgot-reset",array("name"=>$user['desperson'],"code"=>$_GET['code']));
});


//rota aonde  faz a validação de senha novamente
$app->post('/admin/forgot/reset',function (){

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

	$admin=new PageAdmin(["header"=>false,"footer"=>false]);

 	$admin->setTlp("forgot-reset-success");
});




//rota para excluir
//nao podemos usar o metodo delete
//pois para o slim receber o metodo delete vamos ter que mandar via post
// e  ainda  um campo  _methode escrito delete para enteder q vc ta chamando o delete
//pois para a maioria dos servidores web o metodo delete vem desabilitado
/*
$app->delete("/admin/users/:iduser", function ($iduser) {

 	User::verifyLogin();



});

*/



$app->get('/admin', function() {
    
    //verificando sessao do login
   User::verifyLogin();

   $user = User::getFromSession();




	

	$page=new PageAdmin();

	$page->setTlp("index");

	

});





?>