<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model\Usuario;

class Cart{

		const SESSION = "Cart";
		private $idcart;
		private $dessessionid;
		private $iduser;
		private $deszipcode;
		private $vlfreight;
		private $nrdays;


		public function setIdcart($idcart){

			$this->idcart=$idcart;

		}

		public function getIdcart(){


			return $this->idcart;
		}


		public function setDessessionid($dessessionid){

			$this->dessessionid=$dessessionid;

		}

		public function getDessessionid(){


			return $this->dessessionid;
		}


		public function setIduser($iduser){

			$this->iduser=$iduser;

		}

		public function getIduser(){


			return $this->iduser;
		}


		public function setDeszipcode($deszipcode){

			$this->deszipcode=$deszipcode;

		}

		public function getDeszipcode(){


			return $this->deszipcode;
		}


		public function setVlfreight($vlfreight){

			$this->vlfreight=$vlfreight;

		}

		public function getVlfreight(){


			return $this->vlfreight;
		}

		public function setNrdays($nrdays){

			$this->nrdays=$nrdays;

		}

		public function getNrdays(){


			return $this->nrdays;
		}







//salvando carrinho
	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", [
			':idcart'=>$this->getidcart(),
			':dessessionid'=>$this->getDessessionid(),
			':iduser'=>$this->getiduser(),
			':deszipcode'=>$this->getdeszipcode(),
			':vlfreight'=>$this->getvlfreight(),
			':nrdays'=>$this->getnrdays()
		]);


	}


	public function getFromSessionID()
	{

		$sql = new Sql();

		//buscar no banco o id da sessao
		$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
			':dessessionid'=>session_id()
		]);

		if (count($results) > 0) {

			$this->setIdcart($results[0]['idcart']);
			$this->setIduser($results[0]['iduser']);
			$this->setDessessionid($results[0]['dessessionid']);
			$this->setDeszipcode($results[0]['deszipcode']);
			$this->setVlfreight($results[0]['vlfreight']);
			$this->setNrdays($results[0]['nrdays']);

		}

	}	


	public function get(int $idcart)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
			':idcart'=>$idcart
		]);

		if (count($results) > 0) {

			$this->setIdcart($results[0]['idcart']);
			$this->setIduser($results[0]['iduser']);
			$this->setDessessionid($results[0]['dessessionid']);
			$this->setDeszipcode($results[0]['deszipcode']);
			$this->setVlfreight($results[0]['vlfreight']);
			$this->setNrdays($results[0]['nrdays']);

		}

	}


	public function setToSession()
	{

		$_SESSION[Cart::SESSION]["idcart"] = $this->getIdcart();
		$_SESSION[Cart::SESSION]["iduser"] = $this->getIduser();
		$_SESSION[Cart::SESSION]["dessessionid"] = $this->getDessessionid();
		$_SESSION[Cart::SESSION]["deszipcode"] = $this->getDeszipcode();
		$_SESSION[Cart::SESSION]["vlfreight"] = $this->getVlfreight();
		$_SESSION[Cart::SESSION]["nrdays"] = $this->getNrdays();

	}



	public static function getFromSession()
	{

		$cart = new Cart();

		//verifica session  se esta definida e se tem idcart nessa sessao
		if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {

			//se tiver idcart passa ele para bscar no banco pelo metodo get()
			$cart->get((int)$_SESSION[Cart::SESSION]['idcart']);

		} else {//se nao tiver definida ou/e nao tiver idcart

			$cart->getFromSessionID();//verifica se tem id  da sessao


			//se nao houver id da sessao no banco
			if (!(int)$cart->getIdcart() > 0) {


				$data = [
					'dessessionid'=>session_id()
				];


				//verifica se esta logado mas nao com rota de administrador passando false no param
				if (Usuario::checkLogin(false)) {
					


					$user = Usuario::getFromSession();
					
					$data['iduser'] = $user->getIduser();

					$cart->setIduser($data['iduser']);
			$cart->setDessessionid($data['dessessionid']);
			$cart->save();

				$cart->setToSession();	

			}

			

			


			}

		}


		
		return $cart;

	}










}
	













 ?>