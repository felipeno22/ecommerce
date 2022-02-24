<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;


class Address{

	private $desaddress;
	private $descomplement;
	private $desdistrict;
	private $descity;
	private $desstate;
	private $descountry;




	public  function setDesaddress($desaddress){

		$this->desaddress=$desaddress;


	}

	public function  getDesaddress(){

		return $this->desaddress;
	

	}


	public function  setDescomplement($descomplement){

		$this->descomplement=$descomplement;
		$this->descomplement=$descomplement;


	}

	public function  getDescomplement(){

		return $this->descomplement;
	

	}


	public function  setDesdistrict($desdistrict){

		$this->address=$desdistrict;
		$this->desdistrict=$desdistrict;


	}

	public function  getDesdistrict(){

		return $this->desdistrict;
	

	}


	public function  setDescity($descity){

		$this->descity=$descity;
		$this->descity=$descity;


	}

	public function  getDescity(){

		return $this->descity;
	

	}


	public function  setDesstate($desstate){

		$this->desstate=$desstate;
		$this->desstate=$desstate;


	}

	public function  getDesstate(){

		return $this->desstate;
	

	}


	public function  setDescountry($descountry){

		$this->descountry=$descountry;
		$this->descountry=$descountry;


	}

	public function  getDescountry(){

		return $this->descountry;
	

	}
		


}
	


 ?>