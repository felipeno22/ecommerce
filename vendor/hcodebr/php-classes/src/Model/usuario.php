<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Mailer;

class Usuario{

	const SESSION="Usuario";

	//CHAVE´PARA CRIPTOGRAFAR E DESCRIPTOGRAFAR obs: deve ter no minimo 16 caracteres é uma regra
	//NUNCA SUBA ESSA CHAVE NO GITHUB NO REPOSITORIO PUBLICO SE NAO PODEM USAR ELA PARA DESCRIPTOGRAFAR
	const KEY_SECRET="ecommerceFelipe_secret";
	const KEY_SECRET_II = "ecommerceFelipe_secret_2";

	private $iduser;
	private $idperson;
	private $desperson;
	private $deslogin;
	private $despassword;
	private $desemail;
	private $nrphone;
	private $inadmin;

	public function setIduser($iduser){
			$this->iduser=$iduser;


	}

	public function getIduser(){

		return $this->iduser;
	}


	public function setIdperson($idperson){
			$this->idperson=$idperson;


	}

	public function getIdperson(){

		return $this->idperson;
	}

	public function setDesperson($desperson){
			$this->desperson=$desperson;


	}

	public function getDesperson(){

		return $this->desperson;
	}

	public function setDeslogin($deslogin){
			$this->deslogin=$deslogin;


	}

	public function getDeslogin(){

		return $this->deslogin;
	}

	public function setDespassword($despassword){
			$this->despassword=$despassword;


	}

	public function getDespassword(){

		return $this->despassword;
	}

	public function setDesemail($desemail){
			$this->desemail=$desemail;


	}

	public function getDesemail(){

		return $this->desemail;
	}

	public function setNrphone($nrphone){
			$this->nrphone=$nrphone;


	}

	public function getNrphone(){

		return $this->nrphone;
	}


	public function setInadmin($inadmin){
			$this->inadmin=$inadmin;

	}

public function getInadmin(){

	return $this->inadmin;
}



	public  static function login($login,$password){

			$sql=new Sql();


			$result=$sql->select("select * from tb_users where deslogin= :LOGIN",array(":LOGIN"=>$login));

			if(count($result)===0){
				//criando uma exception 
				//Para criar use \  no Exception pois o namespace  Hcode\Model não possui exception
				throw new \Exception ("Usuário inexistente ou  senha inválida!"); 
			}

			//se existir resultado da consulta procegue
			$data=$result[0];

				//var_dump($data);

			/*	array(6) { ["iduser"]=> string(1) "5" ["idperson"]=> string(1) "2" ["deslogin"]=> string(5) "admin" ["despassword "]=> string(32) "21232f297a57a5a743894a0e4a801fc3" ["inadmin"]=> string(1) "1" ["dtregister"]=> string(19) "2022-01-31 15:06:57" }*/


		// verifica se o segundo parametro é igual o primeiro e retorna true or false	
		//password_verify($password, $data['password']);

			//echo md5($password);
			//echo "</br>";
			//echo $data['despassword'];
			//echo "</br>";
			
			if(md5($password)===$data['despassword']){
			

				$user= new Usuario();

					$user->setIdUser($data['iduser']);
					$user->setIdperson($data['idperson']);
					$user->setDeslogin($data['deslogin']);
					$user->setDespassword($data['despassword']);
					$user->setInadmin($data['inadmin']);

					$array_user=[

						"iduser"=> $user->getIdUser(),
						"idperson"=> $user->getIdperson(),
						"deslogin"=> $user->getDeslogin(),
						"despassword"=> $user->getDespassword(),
						"inadmin"=> $user->getInadmin(),

					];

					$_SESSION[Usuario::SESSION]=$array_user;

						/*echo $user->getIdUser();
						echo "<br>";

						echo $user->getIdUser();
						echo "<br>";

						echo $user->getIdperson();
						echo "<br>";


						echo $user->getDeslogin();
						echo "<br>";


						echo $user->getDespassword();
						echo "<br>";

						echo $user->getInadmin();
						echo "<br>";*/

						//	var_dump($user);
						return $user;


			}else{


				throw new \Exception ("Usuário inexistente ou  senha inválida!"); 
			}

			 
		
			


	}


		public static function verifyLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[Usuario::SESSION])
			|| 
			!$_SESSION[Usuario::SESSION]
			||
			!(int)$_SESSION[Usuario::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[Usuario::SESSION]["iduser"] !== $inadmin
		) {
			
			header("Location: /admin/login");
			exit;

		}

	}



public static function logout()
	{

		$_SESSION[Usuario::SESSION] = NULL;

	}

public static function listAll(){

	$sql=new Sql();
	
	

	

return $sql->select('
SELECT * FROM tb_users u
inner join tb_persons p 
on p.idperson=u.idperson  order by u.idperson;
 ');


}


//para salvar user usando procedure	
public  function save($dados){

	
//	var_dump($dados);

	/*array(5) { ["desperson"]=> string(22) "Adrieli Ornela Barbosa" ["deslogin"]=> string(3) "ddd" ["nrphone"]=> string(11) "67984016117" ["desemail"]=> string(23) "ornelaadrieli@gmail.com" ["inadmin" ]=> int(1) }*/

	
	$this->setDesperson($dados["desperson"]);
	$this->setDespassword(md5($dados["despassword"]));
	$this->setDeslogin($dados["deslogin"]);
	$this->setNrphone($dados["nrphone"]);
	$this->setDesemail($dados["desemail"]);
	$this->setInadmin($dados["inadmin"]);


		/*echo $this->getDeslogin();
		echo "<br>";
		echo $this->getDespassword();
		echo "<br>";
		echo $this->getDesemail();
		echo "<br>";
	     echo $this->getNrphone();
	     echo "<br>";
		echo $this->getInadmin();
		echo "<br>";
*/



	$sql=new Sql();

	$sql->select("call sp_users_save(
		:desperson, 
		:deslogin, 
		:despassword, 
		:desemail, 
		:nrphone, 
		:inadmin)",array(":desperson"=>$this->getDesperson(),
			":deslogin"=>$this->getDeslogin(),
			":despassword"=>$this->getDespassword(),
			":desemail"=>$this->getDesemail(),
			":nrphone"=>$this->getNrphone(),
			":inadmin"=>$this->getInadmin()));
	
	

	}



//reponsavel por pegar os dados atraves do id do user
	public  function get($iduser){

	$sql=new Sql();

$result=$sql->select('SELECT * FROM tb_users u
inner join tb_persons p 
on p.idperson=u.idperson  where u.iduser= :iduser',array("iduser"=>$iduser));

		
	
	$this->setIduser($result[0]["iduser"]);
	$this->setIdperson($result[0]["idperson"]);
	$this->setDesperson($result[0]["desperson"]);
	$this->setDespassword($result[0]["despassword"]);
	$this->setDeslogin($result[0]["deslogin"]);
	$this->setNrphone($result[0]["nrphone"]);
	$this->setDesemail($result[0]["desemail"]);
	$this->setInadmin($result[0]["inadmin"]);


		




		/*echo $this->getIdperson();
		echo "<br>";
		echo $this->getDeslogin();
		echo "<br>";
		echo $this->getDespassword();
		echo "<br>";
		echo $this->getDesemail();
		echo "<br>";
	     echo $this->getNrphone();
	     echo "<br>";
		echo $this->getInadmin();
		echo "<br>";
*/


	 

	}



public  function update($dados,$iduser){

	
	//var_dump($dados);
	//echo $iduser;

	/*array(5) { ["desperson"]=> string(22) "Adrieli Ornela Barbosa" ["deslogin"]=> string(3) "ddd" ["nrphone"]=> string(11) "67984016117" ["desemail"]=> string(23) "ornelaadrieli@gmail.com" ["inadmin" ]=> int(1) }*/

	$this->setIduser($iduser);
	$this->setDesperson($dados["desperson"]);
	$this->setDeslogin($dados["deslogin"]);
	$this->setNrphone($dados["nrphone"]);
	$this->setDesemail($dados["desemail"]);
	$this->setInadmin($dados["inadmin"]);


		/*echo $this->getDeslogin();
		echo "<br>";
		echo $this->getDespassword();
		echo "<br>";
		echo $this->getDesemail();
		echo "<br>";
	     echo $this->getNrphone();
	     echo "<br>";
		echo $this->getInadmin();
		echo "<br>";
*/

	$sql=new Sql();

	$result=$sql->select("call sp_usersupdate_save(
		:iduser,
		:desperson, 
		:deslogin, 
		:despassword, 
		:desemail, 
		:nrphone, 
		:inadmin)",array(":iduser"=>$this->getIduser(),
			":desperson"=>$this->getDesperson(),
			":deslogin"=>$this->getDeslogin(),
			":despassword"=>$this->getDespassword(),
			":desemail"=>$this->getDesemail(),
			":nrphone"=>$this->getNrphone(),
			":inadmin"=>$this->getInadmin()));
	
	

	}



public  function delete($iduser){

	
	$sql=new Sql();

	$result=$sql->select("call sp_users_delete(
		:iduser)",array(":iduser"=>$iduser));
	
	

	}


  public static function getForgot($email){

  		$sql=new Sql();


  		//verica se email esta cadastrado
	$results=$sql->select("select * from tb_persons p inner join tb_users using(idperson) where p.desemail=:email",array(":email"=>$email));


//se nao tiver resultado
	if(count($results)===0){

			throw new \Exception ("Não foi possivel recuperar a senha!"); 

	}else{//se tiver


			$data=$results[0];//obtem dados da consulta anteriro

//chama procedure (passando os parametros iduser e o ip do usuario) que faz o cadastrado na tabela userspasswordsrecoveries(recuperção de senhas)
//assim gerando um id do registro na tabela de recupeção de senha
			$results2=$sql->select("call sp_userspasswordsrecoveries_create(
		:iduser,
		:desip 
		)",array(":iduser"=>$data["iduser"],
			":desip"=>$_SERVER['REMOTE_ADDR']
			));


			//essa procedure traz por fim os dados da tabela de recuperação de senha
			if(count($results2)===0){

			throw new \Exception ("Não foi possivel recuperar a senha!"); 

			}else{

				$data2=$results2[0];


				//base64_encode() transforma  codigos em texto/caracteres legiveis
				$code = base64_encode($data2['idrecovery']);

				//gerando um código criptografado do id_recovery da tabela de recuperação de senha 
			
				/*mcrypt_encrypt() é uma função q faz a criptografia obs: essa função é obssoleta apartir php 7.1
				
				$code=base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,Usuario::KEY_SECRET, $data2['id_recovery'],MCRYPT_MODE_ECB));

				*/

				//vamos usar a função openssl_encrypt() para criptografar
				openssl_encrypt($code, 'AES-128-CBC', pack("a16",Usuario::KEY_SECRET), 0, pack("a16", Usuario::KEY_SECRET_II));

				//link para enviar por email usando php mailer para que usuario acesse 
				//nosso sistema para digitar a nova senha
				$link="http://www.ecommercefelipe.com.br/admin/forgot/reset?code= $code";

				//chamando a classe criada phpMailer para fazer o envio  do email usando PHPMAILER
				$mailer= new Mailer($data['desemail'],$data['desperson'],"Redefinir senha da Hcode Store","forgot",array("name"=>$data['desperson'],"link"=>$link));

				//fazedendo o envio do email
				$mailer->send();

				return $data;
			}





	}
	


  }


public static function validForgotDecrypt($code)
	{

	
		//converte de texto para codigo
		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", Usuario::KEY_SECRET), 0, pack("a16", Usuario::KEY_SECRET_II));




		$sql = new Sql();


//sql q faz a validação verificando se existe registro , se nao ja nao foi validado e
//  e se  esta dentro de uma hora do momento q foi cadastro o registro de recuperação de senha	
		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
				AND
				a.dtrecovery IS NULL
				AND
				DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));

		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{

			return $results[0];

		}

	}


	public static function setFogotUsed($idrecovery)
	{

		$sql = new Sql();
		//setando a data de validação da recuperação de senha

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));

	}
	



public  function setPassword($password)
	{

		$sql = new Sql();

		//setando a nova senha no banco de dados 
		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getIduser()
		));

	}


}



?>