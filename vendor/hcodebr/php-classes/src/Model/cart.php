<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model\User;
use \Hcode\Model;

class Cart  extends Model{

		const SESSION = "Cart";
		const SESSION_ERROR = "CartError";



		
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
			if (!(int)$cart->getidcart() > 0) {


				$data = [
					'dessessionid'=>session_id()


				];


				//verifica se esta logado mas nao com rota de administrador passando false no param
				if (User::checkLogin(false)) {
					


					$user = User::getFromSession();
					
					$data['iduser'] = $user->getiduser();

					

			}

				
			
				$cart->setData($data);
				$cart->save();
				$cart->setToSession();	
			


			}

		}


		
		return $cart;

	}
		
//salvando carrinho
	public function save()
	{

		
		$sql = new Sql();

		$results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", [
			':idcart'=>$this->getidcart(),
			':dessessionid'=>$this->getdessessionid(),
			':iduser'=>$this->getiduser(),
			':deszipcode'=>$this->getdeszipcode(),
			':vlfreight'=>$this->getvlfreight(),
			':nrdays'=>$this->getnrdays()
		]);



			$this->setData($results[0]);


	}


	public function getFromSessionID()
	{

		$sql = new Sql();

		//buscar no banco o id da sessao
		$results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
			':dessessionid'=>session_id()
		]);

		if (count($results) > 0) {

			$this->setData($results[0]);
		}

	}	


	public function get(int $idcart)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
			':idcart'=>$idcart
		]);

		if (count($results) > 0) {

			$this->setData($results[0]);
		}

	}


	public function setToSession()
	{

		$_SESSION[Cart::SESSION] = $this->getValues();

	}





public function addProducts(Product $product){

	$sql=new Sql();

	$sql->query("insert into tb_cartsproducts(idcart, idproduct)values(:idcart,:idproduct)",[":idcart"=> $this->getidcart(),"idproduct"=>$product->getidproduct()]);


		$this->getCalculateTotal();
}


public function removeProducts(Product $product, $all=false){

	$sql=new Sql();


	if($all){

		$sql->query("update tb_cartsproducts  set dtremoved= now()  where idcart= :idcart and idproduct=:idproduct and  dtremoved is null ",[":idcart"=> $this->getidcart(),"idproduct"=>$product->getidproduct()]); 


	}else{

			$sql->query("update tb_cartsproducts  set dtremoved= now()  where idcart= :idcart and idproduct=:idproduct and  dtremoved is null limit 1",[":idcart"=> $this->getidcart(),"idproduct"=>$product->getidproduct()]); 

	}

	$this->getCalculateTotal();


}


public function getProducts(){

	$sql=new Sql();


	$rows= $sql->select(" select p.idproduct, p.desproduct, p.vlprice,p.vlwidth,p.vlheight,p.vllength, p.vlweight,p.desurl, count(*) as nrtotal, sum(p.vlprice) as vltotal from tb_cartsproducts cp inner join tb_products p on cp.idproduct=p.idproduct where cp.idcart= :idcart and cp.dtremoved is null group by p.idproduct, p.desproduct, p.vlprice,p.vlwidth,p.vlheight,p.vllength, p.vlweight ,p.desurl order by p.desproduct", [":idcart"=>$this->getidcart()]);



return Product::checkList($rows);




}

public function getProductsTotals(){

		$sql=new Sql();

		$results=$sql->select("select sum(p.vlprice) as vlprice, sum(p.vlwidth) as vlwidth, sum(p.vlheight) as vlheight, sum(p.vllength) as vllength, sum(p.vlweight) as vlweight , count(*) as nrqntd from tb_products p inner join tb_cartsproducts cp on cp.idproduct=p.idproduct
 where cp.idcart= :idcart and cp.dtremoved is null ",[":idcart"=>$this->getidcart()]);


if( count($results)>0){

	return $results[0];

}else{

	return [];
}



}

public function setFreight($nrzipcode){

	$nrzipcode= str_replace("-","", $nrzipcode);

	$totals= $this->getProductsTotals();


	

	if($totals['nrqntd']>0){


		if($totals['vlheight'] <2){
			$totals['vlheight']=2;
		} 
			
		if($totals['vllength'] <16){
		 $totals['vllength']=16;
		}


		if($totals['vlheight'] <15){
		 $totals['vlheight']=15;
		}

		if($totals['vlwidth'] <15){
		 $totals['vlwidth']=15;
		}

		if($totals['vlprice'] <21){
		 $totals['vlprice']=0;
		}


		$qs= http_build_query([
			'nCdEmpresa'=>'',
			'nCdServico'=>"04014",
			'sDsSenha'=>'',
			'sCepOrigem'=>'09853120',
			'sCepDestino'=>$nrzipcode,
			'nVlPeso'=>$totals['vlweight'],
			'nCdFormato'=>'1',
			'nVlComprimento'=>$totals['vllength'],
			'nVlAltura'=>$totals['vlheight'],
			'nVlLargura'=>$totals['vlwidth'],
			'nVlDiametro'=>'0',
			'sCdMaoPropria'=>'N',
			'nVlValorDeclarado'=>$totals['vlprice'],
			'sCdAvisoRecebimento'=>'S']);





		$xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);

			$result = $xml->Servicos->cServico;

			

			if ($result->MsgErro != '') {
				

				Cart::setMsgError($result->MsgErro);

			} else {


				Cart::clearMsgError();

			}

			$this->setnrdays($result->PrazoEntrega);
			$this->setvlfreight(Cart::formatValueToDecimal($result->Valor));
			$this->setdeszipcode($nrzipcode);

			$this->save();

			return $result;


	}else{




	}
}




public static function formatValueToDecimal($value):float
	{

		$value = str_replace('.', '', $value);
		return str_replace(',', '.', $value);

	}

	public static function setMsgError($msg)
	{

		$_SESSION[Cart::SESSION_ERROR] = $msg;

	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : "";

		Cart::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[Cart::SESSION_ERROR] = NULL;

	}



	public function updateFreight()
	{

		if ($this->getdeszipcode() != '') {

			$this->setFreight($this->getdeszipcode());

		}

	}

	public function getValues()
	{

		$this->getCalculateTotal();

		return parent::getValues();

	}


	public function getCalculateTotal()
	{

		$this->updateFreight();

		$totals = $this->getProductsTotals();

		$this->setvlsubtotal($totals['vlprice']);
		$this->setvltotal($totals['vlprice'] + (float)$this->getvlfreight());

	}


	



}
	













 ?>