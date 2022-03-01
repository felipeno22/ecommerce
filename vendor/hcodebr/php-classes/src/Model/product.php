<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;


class Product extends Model{

			

		//lista todas categorias
		public static function listAll(){

			$sql=new Sql();
			$result= $sql->select('
			SELECT * FROM db_ecommerce.tb_products');

			return $result;

		}


		public  function save(){

			

			$sql=new Sql();

			$result=$sql->select("call sp_products_save(:pidproduct,
			:pdesproduct,:pvlprice,:pvlwidth,:pvlheight,:pvllength,:pvlweight,:pdesurl)",array(":pidproduct"=>$this->getidproduct(),
			":pdesproduct"=>$this->getdesproduct(),
			":pvlprice"=>$this->getvlprice(),
			":pvlwidth"=>$this->getvlwidth(),
			":pvlheight"=>$this->getvlheight(),
			":pvllength"=>$this->getvllength(),
			":pvlweight"=>$this->getvlweight(),
			":pdesurl"=>$this->getdesurl()));


		

		}




public  function update(){

	




	$sql=new Sql();


			$result=$sql->select("call sp_products_save(:pidproduct,
			:pdesproduct,:pvlprice,:pvlwidth,:pvlheight,:pvllength,:pvlweight,:pdesurl)",array(":pidproduct"=>$this->getidproduct(),
			":pdesproduct"=>$this->getdesproduct(),
			":pvlprice"=>$this->getvlprice(),
			":pvlwidth"=>$this->getvlwidth(),
			":pvlheight"=>$this->getvlheight(),
			":pvllength"=>$this->getvllength(),
			":pvlweight"=>$this->getvlweight(),
			":pdesurl"=>$this->getdesurl()));

	}





	//reponsavel por pegar os dados atraves do id da categoria
	public  function get($idproduct){

			$sql=new Sql();

			$result=$sql->select('SELECT * FROM tb_products where idproduct= :idproduct',array("idproduct"=>$idproduct));

		
	
				$this->setData($result[0]);


	}




public  function delete($idproduct){

	
	$sql=new Sql();

	$result=$sql->select("delete from tb_products where idproduct= :idproduct",array(":idproduct"=>$idproduct));
	

	}


public function checkPhoto(){
	$caminho='';
//função para verificar se existe foto 

	//verifica se existe foto nesse caminho no caso a foto com nome do id  do produto
	if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'res'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$this->getidproduct().".jpg")){

		 $caminho="/res/admin/img/".$this->getidproduct().".jpg";


	}else{

			 $caminho="/res/admin/img/product.jpg";

	}

		return $this->setdesphoto($caminho);



}	

public function changePhoto($file){
$image='';
	$extension=explode(".",$file['name']);
	$extension=end($extension);

	switch ($extension) {
		case 'jpg':
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


					$destinity=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'res'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$this->getidproduct().".jpg";
					imagejpeg($image,$destinity);
					imagedestroy($image);
					$this->checkPhoto();




	
}



public function getValues()
	{

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}


public static function checkList($list){
	
	foreach ($list as &$row) {
			
			$p = new Product();
			$p->setData($row);
			$row = $p->getValues();

		}

		return $list;
		
		
	
}


public function getFromURL($desurl){

	$sql=new Sql();

	$result=$sql->select("select * from tb_products where desurl= :desurl  limit 1",array(":desurl"=>$desurl));

  	$this->setData($result[0]);
}



public function getCategories(){

	$sql=new Sql();

	
	return $sql->select("select * from tb_categories c inner join tb_productscategories pc on pc.idcategory=c.idcategory where pc.idproduct= :idproduct  ",array(":idproduct"=>$this->getidproduct()));
}


public  static function getPage($page=1, $itemsToPage=10){

			$sql= new Sql();
			$start=($page-1)* $itemsToPage;

			

			$result= $sql->select("select sql_calc_found_rows * FROM db_ecommerce.tb_products  order by idproduct limit ".$start.",".$itemsToPage." ");
		
			$result2= $sql->select("select found_rows() as nrtotal");


	
			return ["data"=>$result,
					"totalItems"=> (int)$result2[0]['nrtotal'],
					"totalPages"=> ceil($result2[0]['nrtotal']/$itemsToPage)];


									


	}


		public  static function getPageSearch($search ,$page=1, $itemsToPage=10){

			$sql= new Sql();
			$start=($page-1)* $itemsToPage;

			

			$result= $sql->select("select sql_calc_found_rows * FROM db_ecommerce.tb_products
			  where desproduct like :search  order by idproduct limit ".$start.",".$itemsToPage." ", ["search"=> "%".$search."%"] );
		
			$result2= $sql->select("select found_rows() as nrtotal");


	
			return ["data"=>$result,
					"totalItems"=> (int)$result2[0]['nrtotal'],
					"totalPages"=> ceil($result2[0]['nrtotal']/$itemsToPage)];


									


	}
	


}


?>