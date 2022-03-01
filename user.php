<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

//chama tela de lista users
$app->get("/admin/users", function () {

 	User::verifyLogin();

 	$search= (isset($_GET['search'])) ? $_GET['search'] :'';
 	$page= (isset($_GET['page'])) ? (int)$_GET['page'] :1;

 	//$users=User::listAll();//chamando todos os usuario

 		if($search != ''){

 		$users=User:: getPageSearch($search,$page);	

 		}else{


 		$users=User:: getPage($page);

 		}

 		$pages=[];

for ($x=0; $x < $users['totalPages'] ; $x++) { 

	array_push($pages, ["href"=>"/admin/users?".http_build_query(["page"=>$x+1,"search"=>$search]),
						"text"=>$x+1]);
}
 	

 	$admin= new PageAdmin();

 	$admin->setTlp("users",array("users"=>$users['data'],
 								  "search"=>$search,
 									"pages"=>$pages));


});



//chama tela de criar user
$app->get("/admin/users/create", function () {

	User::verifyLogin();

 	
 	$admin= new PageAdmin();

 	$admin->setTlp("users-create");


 	
});


/* coloque sempre as rotas na ordem certa
exemplo: /admin/users/:iduser/delete em cima de /admin/users/:iduser
para ele excutar as rotas certinho, se nao pode achar  q as rotas sao iguais e 
  entender somente a rota /admin/users/:iduser */
  //tela para deletar user
$app->get("/admin/users/:iduser/delete", function ($iduser) {

 	User::verifyLogin();


	$user = new User();

	

	

	
	$user->delete($iduser);

 	header("Location: /admin/users");
 	exit;




});

//chama tela de alterar user
$app->get("/admin/users/:iduser", function ($iduser) {

 	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);//convertendo o id passado para int 	


 //	var_dump($user);		
	$admin=new PageAdmin();

 	$admin->setTlp("users-update",array("user"=>$user->getValues()));


});



//rota para cadastrar
$app->post("/admin/users/create", function () {

 	User::verifyLogin();


 		
 	$user = new User();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	//$_POST['despassword'] = md5($_POST['despassword']);

 	$user->setData($_POST);

 	$user->save();

 	header("Location: /admin/users");
 	exit;



});



//rota para alterar
$app->post("/admin/users/:iduser", function ($iduser) {


 	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->get((int)$iduser);//convertendo o id passado para int 	


	$user->setData($_POST);

	//var_dump($user);
	$user->update();

 	header("Location: /admin/users");
 	exit;




});





?>