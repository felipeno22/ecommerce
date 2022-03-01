<?php

namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;


class Category extends Model{

	




		//lista todas categorias
		public static function listAll(){

			$sql=new Sql();
			$result= $sql->select('
			SELECT * FROM db_ecommerce.tb_categories');

			return $result;

		}


		public  function save(){

	
			$sql=new Sql();

			$result=$sql->select("call sp_categories_save(:pidcategory,
			:pdescategory)",array(":pidcategory"=>$this->getidcategory(),
			":pdescategory"=>$this->getdescategory()));

			Category::updateFile();

		}




public  function update(){

	
	

	$sql=new Sql();

	/*$result=$sql->select("update tb_categories set descategory=:descategory where idcategory=:idcategory",array(":idcategory"=>$this->getIdcategory(),
			":descategory"=>$this->getDescategory()));*/

			$result=$sql->select("call sp_categories_save(:pidcategory,
			:pdescategory)",array(":pidcategory"=>$this->getidcategory(),
			":pdescategory"=>$this->getdescategory()));
	
	Category::updateFile();

	}





	//reponsavel por pegar os dados atraves do id da categoria
	public  function get($idcategory){

			$sql=new Sql();

			$result=$sql->select('SELECT * FROM tb_categories where idcategory= :idcategory',array("idcategory"=>$idcategory));

		
	
	$this->setidcategory($result[0]["idcategory"]);
	$this->setdescategory($result[0]["descategory"]);
	$this->setdtregister($result[0]["dtregister"]);



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
				where pc.idcategory= :idcategory)",["idcategory"=>$this->getidcategory()]);

		}else{
			$result= $sql->select("select * from tb_products where idproduct NOT in(
				select p.idproduct from tb_products p
				inner join tb_productscategories pc on p.idproduct=pc.idproduct
				where pc.idcategory= :idcategory)",["idcategory"=>$this->getidcategory()]);

		}

			

		return $result;
	}


	public function getProductsPage($page=1, $itemsToPage=3){

			$sql= new Sql();
			$start=($page-1)* $itemsToPage;

			

			$result= $sql->select("select sql_calc_found_rows * from tb_products p
									inner join tb_productscategories pc on pc.idproduct=p.idproduct
									inner join tb_categories c on pc.idcategory=c.idcategory
									where c.idcategory= :idcategory limit ".$start.",".$itemsToPage."  ",[":idcategory"=>$this->getidcategory()]);
		
			$result2= $sql->select("select found_rows() as nrtotal");


//ceil() arredonda o valor para cima ,
// aqui nesse caso se nao dar para distribuir certinho
//o num de produtos desejado pelo num de paginas desejada
//ele cria mais uma pagina para colocar o restante			
			return ["data"=>Product::checkList($result),
					"totalItems"=> (int)$result2[0]['nrtotal'],
					"totalPages"=> ceil($result2[0]['nrtotal']/$itemsToPage)];


									


	}




	public function addProduct(Product $product){

			$sql=new Sql();

			$sql->query("insert into tb_productscategories( idcategory,idproduct)value(:idcategory,:idproduct)",
			["idcategory"=>$this->getidcategory(),
			  "idproduct"=>$product->getidproduct()]);

	}

	public function removeProduct(Product $product){

			$sql=new Sql();

			$sql->query("delete from tb_productscategories 
				where idcategory= :idcategory and idproduct=:idproduct",
				["idcategory"=>$this->getidcategory(),"idproduct"=>$product->getidproduct()]);

	}



	public  static function getPage($page=1, $itemsToPage=10){

			$sql= new Sql();
			$start=($page-1)* $itemsToPage;

			

			$result= $sql->select("select sql_calc_found_rows *  FROM db_ecommerce.tb_categories limit ".$start.",".$itemsToPage." ");
		
			$result2= $sql->select("select found_rows() as nrtotal");


	
			return ["data"=>$result,
					"totalItems"=> (int)$result2[0]['nrtotal'],
					"totalPages"=> ceil($result2[0]['nrtotal']/$itemsToPage)];


									


	}


		public  static function getPageSearch($search ,$page=1, $itemsToPage=10){

			$sql= new Sql();
			$start=($page-1)* $itemsToPage;

			

			$result= $sql->select("select sql_calc_found_rows *  FROM db_ecommerce.tb_categories 
					 where descategory like :search  order by idcategory limit ".$start.",".$itemsToPage." ", ["search"=> "%".$search."%"] );
		
			$result2= $sql->select("select found_rows() as nrtotal");


	
			return ["data"=>$result,
					"totalItems"=> (int)$result2[0]['nrtotal'],
					"totalPages"=> ceil($result2[0]['nrtotal']/$itemsToPage)];


									


	}
	



}


?>