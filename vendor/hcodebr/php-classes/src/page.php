<?php


namespace Hcode;

use Rain\Tpl;


class Page{

	private $tpl;
	private $defaults=[
		"header"=>true,
		"footer"=>true,
		"data"=>[]];
	private $options=[];


	public function __construct($opts=array(),$tpl_dir="/views/"){


		//$this->$defaults["data"]["session"]=$_SESSION;
		$this->options=array_merge($this->defaults,$opts);
		


			//array de configuração do tpl
	$config=array(
		"tpl_dir"=>$_SERVER['DOCUMENT_ROOT'].$tpl_dir,
		"cache_dir"=>$_SERVER['DOCUMENT_ROOT']."/cache-views/",
		 "debug"         => false

	);


	Tpl::configure($config);
	$this->tpl=new Tpl();

	
//	$this->foreachPage($options["data"]);


		// se o parametro header for ativo
		//chama ele ou seja se for chamada a rota da tela de login

	if ($this->options['header'] === true) $this->tpl->draw("header");




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

		// se o parametro footer for ativo
		//chama ele ou seja se for chamada a rota da tela de login
		if ($this->options['footer'] === true) $this->tpl->draw("footer");

	}


	





}


?>