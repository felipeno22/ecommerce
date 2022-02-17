<?php

use \Hcode\Model\Usuario;

function formatPrice($vlprice){
	
		return number_format($vlprice,2,",",".");
}


function checkLogin($inadmin=true){


	return Usuario::checkLogin($inadmin);
}

function getUserName(){

$user= Usuario::getFromSession();

return $user->getDesperson();

}


?>