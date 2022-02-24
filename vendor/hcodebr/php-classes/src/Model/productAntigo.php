<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;


class Product{

	
	private $idproduct;
	private $desproduct;
	private $vlprice;
	private $vlwidth ;
	private $vlheight;
	private $vllength;
	private $vlweight;
	private $desurl;
	private $desphoto;


	
	public function setIdproduct($idproduct){
			$this->idproduct=$idproduct;


	}

	public function getIdproduct(){

		return $this->idproduct;
	}



	public function setDesproduct($desproduct){
			$this->desproduct=$desproduct;


	}

	public function getDesproduct(){

		return $this->desproduct;
	}

	public function setVlprice($vlprice){
			$this->vlprice=$vlprice;

	}

	public function getVlprice(){

		return $this->vlprice;
	}


	public function setVlwidth($vlwidth){
			$this->vlwidth=$vlwidth;

	}


	public function getVlwidth(){

		return $this->vlwidth;
	}

	public function setVlheight($vlheight){
			$this->vlheight=$vlheight;

	}


	public function getVlheight(){

		return $this->vlheight;
	}



	public function setVllengh($vllength){
			$this->vllength=$vllength;

	}


	public function getVllength(){

		return $this->vllength;
	}


	public function setVlweight($vlweight){
			$this->vlweight=$vlweight;

	}


	public function getVlweight(){

		return $this->vlweight;
	}



	public function setDesurl($desurl){
			$this->desurl=$desurl;


	}

	public function getDesurl(){

		return $this->desurl;
	}

	public function setDesphoto($desphoto){
			$this->desphoto=$desphoto;


	}

	public function getDesphoto(){

		return $this->desphoto;
	}




		//lista todas categorias
		public static function listAll(){

			$sql=new Sql();
			$result= $sql->select('
			SELECT * FROM db_ecommerce.tb_products');

			return $result;

		}


		public  function save($dados){

			$this->setDesproduct($dados["desproduct"]);
				$this->setVlprice($dados["vlprice"]);
			$this->setVlwidth($dados["vlwidth"]);
			$this->setVlheight($dados["vlheight"]);
			$this->setVllengh($dados["vllength"]);
			$this->setVlweight($dados["vlweight"]);
			$this->setDesurl($dados["desurl"]);

			$sql=new Sql();

			$result=$sql->select("call sp_products_save(:pidproduct,
			:pdesproduct,:pvlprice,:pvlwidth,:pvlheight,:pvllength,:pvlweight,:pdesurl)",array(":pidproduct"=>$this->getIdproduct(),
			":pdesproduct"=>$this->getDesproduct(),
			":pvlprice"=>$this->getVlprice(),
			":pvlwidth"=>$this->getVlwidth(),
			":pvlheight"=>$this->getVlheight(),
			":pvllength"=>$this->getVllength(),
			":pvlweight"=>$this->getVlweight(),
			":pdesurl"=>$this->getDesurl()));


		

		}




public  function update($dados,$idproduct){

	
	$this->setIdproduct($idproduct);
	$this->setDesproduct($dados["desproduct"]);
	$this->setVlprice($dados["vlprice"]);
	$this->setVlwidth($dados["vlwidth"]);
	$this->setVlheight($dados["vlheight"]);
	$this->setVllengh($dados["vllength"]);
	$this->setVlweight($dados["vlweight"]);
	$this->setDesurl($dados["desurl"]);



	$sql=new Sql();


			$result=$sql->select("call sp_products_save(:pidproduct,
			:pdesproduct,:pvlprice,:pvlwidth,:pvlheight,:pvllength,:pvlweight,:pdesurl)",array(":pidproduct"=>$this->getIdproduct(),
			":pdesproduct"=>$this->getDesproduct(),
			":pvlprice"=>$this->getVlprice(),
			":pvlwidth"=>$this->getVlwidth(),
			":pvlheight"=>$this->getVlheight(),
			":pvllength"=>$this->getVllength(),
			":pvlweight"=>$this->getVlweight(),
			":pdesurl"=>$this->getDesurl()));

	}





	//reponsavel por pegar os dados atraves do id da categoria
	public  function get($idproduct){

			$sql=new Sql();

			$result=$sql->select('SELECT * FROM tb_products where idproduct= :idproduct',array("idproduct"=>$idproduct));

		
	
	$this->setIdproduct($result[0]["idproduct"]);
	$this->setDesproduct($result[0]["desproduct"]);
	$this->setVlprice($result[0]["vlprice"]);
	$this->setVlwidth($result[0]["vlwidth"]);
	$this->setVlheight($result[0]["vlheight"]);
	$this->setVllengh($result[0]["vllength"]);
	$this->setVlweight($result[0]["vlweight"]);
	$this->setDesurl($result[0]["desurl"]);


	}




public  function delete($idproduct){

	
	$sql=new Sql();

	$result=$sql->select("delete from tb_products where idproduct= :idproduct",array(":idproduct"=>$idproduct));
	

	}


public function checkPhoto(){
	$caminho='';
//função para verificar se existe foto 

	//verifica se existe foto nesse caminho no caso a foto com nome do id  do produto
	if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'res'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$this->getIdproduct().".jpg")){

		 $caminho="/res/admin/img/".$this->getIdproduct().".jpg";

	}else{

			 $caminho="/res/admin/img/product.jpg";

	}

		return $this->setDesphoto($caminho);



}	

public function changePhoto($file){

	$extension=explode(".",$file['name']);
	$extension=end($extension);

	switch ($extension) {
		case 'jpg':
			
				//função do GD q é de arquivos do php
				//passando por parametro o arquivo tmp
				$image=imagecreatefromjpeg($file['tmp_name']);



			break;
		case 'jpeg':
			
				$image=imagecreatefromjpeg($file['tmp_name']);


			break;
		case 'gif':
				$image=imagecreatefromgif($file['tmp_name']);



			break;
		case 'png':
			

				$image=imagecreatefrompng($file['tmp_name']);



			break;
	
	}

	$destinity=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'res'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$this->getIdproduct().".jpg";

	imagejpeg($image,$destinity);

	imagedestroy($image);

	$this->checkPhoto();
}

public static function checkList($list){
	$array=[];

//o '&''  server para manipular a mesma váriavel na memoria

	foreach ($list as $row) {
		
		
		$p= new Product();
		$p->setIdproduct($row["idproduct"]);
		$p->setDesproduct($row["desproduct"]);
		$p->setVlprice($row["vlprice"]);
		$p->setVlwidth($row["vlwidth"]);
		$p->setVlheight($row["vlheight"]);
		$p->setVllengh($row["vllength"]);
		$p->setVlweight($row["vlweight"]);
		$p->setDesurl($row["desurl"]);
		$p->checkPhoto();
		//$p->setDesphoto($p->getDesphoto());
		$array[]=$p;
	}
	
	return $array;
}


public function getFromURL($desurl){

	$sql=new Sql();

	$result=$sql->select("select * from tb_products where desurl= :desurl  limit 1",array(":desurl"=>$desurl));

  	$this->setIdproduct($result[0]["idproduct"]);
	$this->setDesproduct($result[0]["desproduct"]);
	$this->setVlprice($result[0]["vlprice"]);
	$this->setVlwidth($result[0]["vlwidth"]);
	$this->setVlheight($result[0]["vlheight"]);
	$this->setVllengh($result[0]["vllength"]);
	$this->setVlweight($result[0]["vlweight"]);
	$this->setDesurl($result[0]["desurl"]);
	$this->checkPhoto();
}



public function getCategories(){

	$sql=new Sql();

	
	return $sql->select("select * from tb_categories c inner join tb_productscategories pc on pc.idcategory=c.idcategory where pc.idproduct= :idproduct  ",array(":idproduct"=>$this->getIdproduct()));
}

}


?>