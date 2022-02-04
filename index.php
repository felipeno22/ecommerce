<?php 


//iniciando sessao
session_start();
require_once("vendor/autoload.php");


//use Hcode\DB\Sql;
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\Usuario;
use \Hcode\Model\Category;

$app = new Slim();

$app->config('debug', true);

$app->get('/admin', function() {
    
    //verificando sessao do login
    Usuario:: verifyLogin();


	//echo "OK";

	$page=new PageAdmin();

	$page->setTlp("index");

	

});

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
	Usuario::login($_POST['login'],$_POST['password']);



	//chamando a tela de adminstração após logar
	header("Location: /admin");
	exit;
	

});


//criando rota para a tela de  login
$app->get('/admin/logout', function() {
    

   Usuario::logout();


	//chamando a tela de adminstração após logar
	header("Location: /admin/login");
	exit;
	

});



//chama tela de lista users
$app->get("/admin/users", function () {

 	Usuario::verifyLogin();

 	$users=Usuario::listAll();//chamando todos os usuario


 	$admin= new PageAdmin();

 	$admin->setTlp("users",array("users"=>$users));


});



//chama tela de criar user
$app->get("/admin/users/create", function () {

	Usuario::verifyLogin();

 	
 	$admin= new PageAdmin();

 	$admin->setTlp("users-create");


 	
});


/* coloque sempre as rotas na ordem certa
exemplo: /admin/users/:iduser/delete em cima de /admin/users/:iduser
para ele excutar as rotas certinho, se nao pode achar  q as rotas sao iguais e 
  entender somente a rota /admin/users/:iduser */
  //tela para deletar user
$app->get("/admin/users/:iduser/delete", function ($iduser) {

 	Usuario::verifyLogin();


	$user = new Usuario();

	

	

	
	$user->delete($iduser);

 	header("Location: /admin/users");
 	exit;




});

//chama tela de alterar user
$app->get("/admin/users/:iduser", function ($iduser) {

 	Usuario::verifyLogin();

	$user = new Usuario();

	$user->get((int)$iduser);//convertendo o id passado para int 	

 //	var_dump($user);		
	$admin=new PageAdmin();

 	$admin->setTlp("users-update",array("iduser"=>$user->getIduser(),
 										"desperson"=>$user->getDesperson(),
 										"deslogin"=>$user->getDeslogin(),
 										"despassword"=>$user->getDespassword(),
 										"desemail"=>$user->getDesemail(),
 										"nrphone"=>$user->getNrphone(),
 										"inadmin"=>$user->getInadmin()




 ));


});
/**/




//rota para cadastrar
$app->post("/admin/users/create", function () {

 	Usuario::verifyLogin();


 		
 	$user = new Usuario();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	

 	$user->save($_POST);

 	header("Location: /admin/users");
 	exit;



	/*$user = new User();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
 	exit;*/


});



//rota para alterar
$app->post("/admin/users/:iduser", function ($iduser) {


 	Usuario::verifyLogin();

	$user = new Usuario();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->get((int)$iduser);//convertendo o id passado para int 	

	//var_dump($user);
	$user->update($_POST,$iduser);

 	header("Location: /admin/users");
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
 	$user= Usuario::getForgot($_POST["email"]);

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
	$user= Usuario::validForgotDecrypt($_GET['code']);

	$admin=new PageAdmin(["header"=>false,"footer"=>false]);

 	$admin->setTlp("forgot-reset",array("name"=>$user['desperson'],"code"=>$_GET['code']));
});


//rota aonde  faz a validação de senha novamente
$app->post('/admin/forgot/reset',function (){

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

 	Usuario::verifyLogin();



});

*/


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



$app->run();//dps dde carregado os arquivos ele roda

 ?>