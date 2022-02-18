<?php

use \Hcode\Model\Usuario;

function formatPrice($vlprice){
	
		if (!$vlprice > 0) $vlprice = 0;
		
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