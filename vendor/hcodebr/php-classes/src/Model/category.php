<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;


class Category{

	

	private $idcategory;
	private $descategory;
	private $dtregister;
	
	public function setIdcategory($idcategory){
			$this->idcategory=$idcategory;


	}

	public function getIdcategory(){

		return $this->idcategory;
	}



	public function setDescategory($descategory){
			$this->descategory=$descategory;


	}

	public function getDescategory(){

		return $this->descategory;
	}

	public function setDtregister($dtregister){
			$this->dtregister=$dtregister;

	}

	public function getDtregister(){

		return $this->dtregister;
	}



		//lista todas categorias
		public static function listAll(){

			$sql=new Sql();
			$result= $sql->select('
			SELECT * FROM db_ecommerce.tb_categories');

			return $result;

		}


		public  function save($dados){

			$this->setDescategory($dados["descategory"]);
			$sql=new Sql();

			$result=$sql->select("call sp_categories_save(:pidcategory,
			:pdescategory)",array(":pidcategory"=>$this->getIdcategory(),
			":pdescategory"=>$this->getDescategory()));


		}




public  function update($dados,$idcategory){

	
	$this->setIdcategory($idcategory);
	$this->setDescategory($dados["descategory"]);


	$sql=new Sql();

	/*$result=$sql->select("update tb_categories set descategory=:descategory where idcategory=:idcategory",array(":idcategory"=>$this->getIdcategory(),
			":descategory"=>$this->getDescategory()));*/

			$result=$sql->select("call sp_categories_save(:pidcategory,
			:pdescategory)",array(":pidcategory"=>$this->getIdcategory(),
			":pdescategory"=>$this->getDescategory()));
	
	

	}





	//reponsavel por pegar os dados atraves do id da categoria
	public  function get($idcategory){

			$sql=new Sql();

			$result=$sql->select('SELECT * FROM tb_categories where idcategory= :idcategory',array("idcategory"=>$idcategory));

		
	
	$this->setIdcategory($result[0]["idcategory"]);
	$this->setDescategory($result[0]["descategory"]);
	$this->setDtregister($result[0]["dtregister"]);



	}




public  function delete($idcategory){

	
	$sql=new Sql();

	$result=$sql->select("delete from tb_categories where idcategory= :idcategory",array(":idcategory"=>$idcategory));
	
	

	}


}


?>