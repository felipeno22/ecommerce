<?php


namespace Hcode;

use Rain\Tpl;


class Page{

	private $tpl;
	private $defaults=["data"=>[]];
	private $options=[];


	public function __construct($opts=array()){

		$options=array_merge($this->defaults,$opts);



			//array de configuração do tpl
	$config=array(
		"tpl_dir"=>$_SERVER['DOCUMENT_ROOT']."/views/",
		"cache_dir"=>$_SERVER['DOCUMENT_ROOT']."/cache-views/"

	);


	Tpl::configure($config);
	$this->tpl=new Tpl();

	
	$this->foreachPage($options["data"]);

	$this->tpl->draw("header");



	}


	private function foreachPage($data=array()){

			foreach ($data as $key => $value) {
		# code...
		$this->tpl->assign($key,$value);
	}

	}


	public function setTlp($name,$data=array(),$returnHtml=false){


			$this->foreachPage($data);

			return $this->tpl->draw($name,$returnHtml);





	}



	public function __destruct(){

		$this->tpl->draw("footer");

	}


	





}


?>