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

			Category::updateFile();

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
	
	Category::updateFile();

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
	
	Category::updateFile();

	}


	public static function updateFile(){

		$category=Category::listAll();

		$html=[];

		foreach ($category as $cat) {
			array_push($html,'<li><a href="/categories/'.$cat["idcategory"].' ">'.$cat['descategory'].'</a></li>' );
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'categories-menu.html', implode('',$html));



	}


	public  function getProducts($related=true){

		$sql= new Sql();
		$products=new Product();
		$result='';

		if($related===true){

			$result= $sql->select("select * from tb_products where idproduct in(
				select p.idproduct from tb_products p
				inner join tb_productscategories pc on p.idproduct=pc.idproduct
				where pc.idcategory= :idcategory)",["idcategory"=>$this->getIdcategory()]);

		}else{
			$result= $sql->select("select * from tb_products where idproduct NOT in(
				select p.idproduct from tb_products p
				inner join tb_productscategories pc on p.idproduct=pc.idproduct
				where pc.idcategory= :idcategory)",["idcategory"=>$this->getIdcategory()]);

		}

			

		return $result;
	}

	public function addProduct(Product $product){

			$sql=new Sql();

			$sql->query("insert into tb_productscategories( idcategory,idproduct)value(:idcategory,:idproduct)",
			["idcategory"=>$this->getIdcategory(),
			  "idproduct"=>$product->getIdproduct()]);

	}

	public function removeProduct(Product $product){

			$sql=new Sql();

			$sql->query("delete from tb_productscategories 
				where idcategory= :idcategory and idproduct=:idproduct",
				["idcategory"=>$this->getIdcategory(),"idproduct"=>$product->getIdproduct()]);

	}


}


?>